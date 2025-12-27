<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Queue;
use App\Models\AutomationLog;
use App\Mail\TrialExpiryReminder;
use App\Mail\InactivityNudge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RunAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run automated emails (trial reminders, inactivity nudges, etc.)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running automation tasks...');

        $emailsSent = 0;

        // Trial expiry reminders
        $this->info('Checking trial expiry reminders...');
        $emailsSent += $this->sendTrialExpiryReminders();

        // Inactivity nudges
        $this->info('Checking inactivity nudges...');
        $emailsSent += $this->sendInactivityNudges();

        $this->info("Automation complete. {$emailsSent} email(s) sent.");

        return Command::SUCCESS;
    }

    /**
     * Send trial expiry reminder emails
     */
    private function sendTrialExpiryReminders(): int
    {
        $emailsSent = 0;
        $now = now();
        
        // Get tenants with active trials
        $tenantsOnTrial = Tenant::where('is_active', true)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', $now->copy()->subDays(1)) // Not expired yet
            ->get();

        foreach ($tenantsOnTrial as $tenant) {
            // Skip if tenant has an active paid subscription (not on trial anymore)
            $activeSubscription = Subscription::where('tenant_id', $tenant->id)
                ->where('status', Subscription::STATUS_ACTIVE)
                ->first();
            
            if ($activeSubscription) {
                $plan = \App\Models\Plan::find($activeSubscription->plan_id);
                if ($plan && $plan->slug !== 'trial') {
                    continue; // Tenant has active paid subscription, skip trial reminder
                }
            }

            $daysUntilExpiry = $now->diffInDays($tenant->trial_ends_at, false);

            // 7 days before expiry
            if ($daysUntilExpiry === 7) {
                if (!AutomationLog::wasSentRecently($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, '7_days', 24)) {
                    try {
                        $primaryUser = $tenant->users()->wherePivot('role', 'admin')->first() 
                            ?? $tenant->users()->first();
                        
                        if ($primaryUser) {
                            Mail::to($primaryUser->email)->send(new TrialExpiryReminder($tenant, 7));
                            AutomationLog::logSent($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, '7_days', [
                                'days_remaining' => 7,
                                'recipient' => $primaryUser->email,
                            ]);
                            $emailsSent++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send 7-day trial reminder', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // 3 days before expiry
            if ($daysUntilExpiry === 3) {
                if (!AutomationLog::wasSentRecently($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, '3_days', 24)) {
                    try {
                        $primaryUser = $tenant->users()->wherePivot('role', 'admin')->first() 
                            ?? $tenant->users()->first();
                        
                        if ($primaryUser) {
                            Mail::to($primaryUser->email)->send(new TrialExpiryReminder($tenant, 3));
                            AutomationLog::logSent($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, '3_days', [
                                'days_remaining' => 3,
                                'recipient' => $primaryUser->email,
                            ]);
                            $emailsSent++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send 3-day trial reminder', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // On expiry date (same day)
            if ($daysUntilExpiry === 0 && $tenant->trial_ends_at->isSameDay($now)) {
                if (!AutomationLog::wasSentRecently($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, 'expired', 24)) {
                    try {
                        $primaryUser = $tenant->users()->wherePivot('role', 'admin')->first() 
                            ?? $tenant->users()->first();
                        
                        if ($primaryUser) {
                            Mail::to($primaryUser->email)->send(new TrialExpiryReminder($tenant, 0));
                            AutomationLog::logSent($tenant->id, AutomationLog::TYPE_TRIAL_REMINDER, 'expired', [
                                'days_remaining' => 0,
                                'recipient' => $primaryUser->email,
                            ]);
                            $emailsSent++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send trial expiry email', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return $emailsSent;
    }

    /**
     * Send inactivity nudge emails
     */
    private function sendInactivityNudges(): int
    {
        $emailsSent = 0;
        $threeDaysAgo = Carbon::now()->subDays(3);
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Get all active tenants
        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            // Skip if tenant is a Super Admin test tenant
            $adminUser = $tenant->users()->where('is_super_admin', true)->first();
            if ($adminUser) {
                continue;
            }

            // Skip if tenant has cancelled subscription
            $subscription = $tenant->subscription;
            if ($subscription && $subscription->status === Subscription::STATUS_CANCELLED) {
                continue;
            }

            // Check if tenant has any queues created in the relevant period (use withoutGlobalScopes)
            $recentQueues = Queue::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('created_at', '>=', $threeDaysAgo)
                ->exists();

            if ($recentQueues) {
                continue; // Tenant is active, skip
            }

            // Check if tenant was created recently (within 30 days = new tenant)
            $isNewTenant = $tenant->created_at && $tenant->created_at->greaterThan($thirtyDaysAgo);
            $thresholdDays = $isNewTenant ? 3 : 7;
            $thresholdDate = $isNewTenant ? $threeDaysAgo : $sevenDaysAgo;

            // Check last queue activity
            $lastQueue = Queue::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastQueue && $lastQueue->created_at->lessThan($thresholdDate)) {
                // Check cooldown (7 days for inactivity nudges to avoid spam)
                if (!AutomationLog::wasSentRecently($tenant->id, AutomationLog::TYPE_INACTIVITY_NUDGE, null, 168)) { // 168 hours = 7 days
                    try {
                        $primaryUser = $tenant->users()->wherePivot('role', 'admin')->first() 
                            ?? $tenant->users()->first();
                        
                        if ($primaryUser) {
                            Mail::to($primaryUser->email)->send(new InactivityNudge($tenant, $isNewTenant));
                            AutomationLog::logSent($tenant->id, AutomationLog::TYPE_INACTIVITY_NUDGE, null, [
                                'is_new_tenant' => $isNewTenant,
                                'days_inactive' => $lastQueue->created_at->diffInDays(now()),
                                'recipient' => $primaryUser->email,
                            ]);
                            $emailsSent++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send inactivity nudge', [
                            'tenant_id' => $tenant->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            } elseif (!$lastQueue) {
                // Tenant has never created a queue
                if ($tenant->created_at && $tenant->created_at->lessThan($thresholdDate)) {
                    if (!AutomationLog::wasSentRecently($tenant->id, AutomationLog::TYPE_INACTIVITY_NUDGE, null, 168)) {
                        try {
                            $primaryUser = $tenant->users()->wherePivot('role', 'admin')->first() 
                                ?? $tenant->users()->first();
                            
                            if ($primaryUser) {
                                Mail::to($primaryUser->email)->send(new InactivityNudge($tenant, $isNewTenant));
                                AutomationLog::logSent($tenant->id, AutomationLog::TYPE_INACTIVITY_NUDGE, null, [
                                    'is_new_tenant' => $isNewTenant,
                                    'days_since_creation' => $tenant->created_at->diffInDays(now()),
                                    'recipient' => $primaryUser->email,
                                ]);
                                $emailsSent++;
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to send inactivity nudge', [
                                'tenant_id' => $tenant->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        return $emailsSent;
    }
}
