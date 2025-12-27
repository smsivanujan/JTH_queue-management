<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Tenant;
use App\Models\Queue;
use App\Models\Subscription;
use App\Models\ActiveScreen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for alerts (churn risk, payment risk, system health)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for alerts...');

        $alertsTriggered = 0;

        // Check churn risk alerts
        $this->info('Checking churn risk...');
        $alertsTriggered += $this->checkChurnRisk();

        // Check payment risk alerts
        $this->info('Checking payment risk...');
        $alertsTriggered += $this->checkPaymentRisk();

        // Check system health alerts
        $this->info('Checking system health...');
        $alertsTriggered += $this->checkSystemHealth();

        $this->info("Alert check complete. {$alertsTriggered} alert(s) triggered.");

        return Command::SUCCESS;
    }

    /**
     * Check for churn risk alerts
     * - New tenants: No queues in 3 days
     * - Active tenants: No queues in 7 days
     */
    private function checkChurnRisk(): int
    {
        $alertsTriggered = 0;
        $threeDaysAgo = Carbon::now()->subDays(3);
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Get all active tenants
        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            // Check if tenant has any queues created in the relevant period
            // Use withoutGlobalScopes to bypass TenantScope since we're explicitly filtering by tenant_id
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
            // Use withoutGlobalScopes to bypass TenantScope since we're explicitly filtering by tenant_id
            $lastQueue = Queue::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastQueue && $lastQueue->created_at->lessThan($thresholdDate)) {
                // Trigger alert
                $alert = Alert::findOrCreateByType(
                    Alert::TYPE_CHURN_RISK,
                    $tenant->id,
                    [
                        'severity' => $isNewTenant ? Alert::SEVERITY_HIGH : Alert::SEVERITY_MEDIUM,
                        'title' => "Churn Risk: {$tenant->name}",
                        'message' => "No queue activity in the last {$thresholdDays} days. Tenant may be inactive or at risk of churn.",
                        'metadata' => [
                            'days_inactive' => $lastQueue->created_at->diffInDays(now()),
                            'is_new_tenant' => $isNewTenant,
                            'last_queue_date' => $lastQueue->created_at->toDateString(),
                        ],
                    ]
                );

                // Only trigger if not triggered in last 24h
                if (!$alert->wasTriggeredRecently()) {
                    $alert->updateLastTriggered();
                    $this->sendAlertNotification($alert);
                    $alertsTriggered++;
                }
            } elseif (!$lastQueue) {
                // Tenant has never created a queue
                if ($tenant->created_at && $tenant->created_at->lessThan($thresholdDate)) {
                    $alert = Alert::findOrCreateByType(
                        Alert::TYPE_CHURN_RISK,
                        $tenant->id,
                        [
                            'severity' => $isNewTenant ? Alert::SEVERITY_HIGH : Alert::SEVERITY_MEDIUM,
                            'title' => "Churn Risk: {$tenant->name}",
                            'message' => "No queues have been created. Tenant may need onboarding assistance.",
                            'metadata' => [
                                'days_since_creation' => $tenant->created_at->diffInDays(now()),
                                'is_new_tenant' => $isNewTenant,
                            ],
                        ]
                    );

                    if (!$alert->wasTriggeredRecently()) {
                        $alert->updateLastTriggered();
                        $this->sendAlertNotification($alert);
                        $alertsTriggered++;
                    }
                }
            }
        }

        return $alertsTriggered;
    }

    /**
     * Check for payment risk alerts
     * - Trial expiring in 3 days
     * - Subscription past_due
     */
    private function checkPaymentRisk(): int
    {
        $alertsTriggered = 0;
        $threeDaysFromNow = Carbon::now()->addDays(3);

        // Check trial expiring soon
        $tenantsOnTrial = Tenant::where('is_active', true)
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', $threeDaysFromNow)
            ->where('trial_ends_at', '>', now())
            ->get();

        foreach ($tenantsOnTrial as $tenant) {
            $daysRemaining = now()->diffInDays($tenant->trial_ends_at, false);

            $alert = Alert::findOrCreateByType(
                Alert::TYPE_PAYMENT_RISK,
                $tenant->id,
                [
                    'severity' => $daysRemaining <= 1 ? Alert::SEVERITY_HIGH : Alert::SEVERITY_MEDIUM,
                    'title' => "Trial Expiring: {$tenant->name}",
                    'message' => "Trial expires in {$daysRemaining} day(s). Consider reaching out to encourage subscription.",
                    'metadata' => [
                        'trial_ends_at' => $tenant->trial_ends_at->toDateString(),
                        'days_remaining' => $daysRemaining,
                    ],
                ]
            );

            if (!$alert->wasTriggeredRecently()) {
                $alert->updateLastTriggered();
                $this->sendAlertNotification($alert);
                $alertsTriggered++;
            }
        }

        // Check past due subscriptions (if status field supports this)
        // Note: This assumes subscriptions have a status field that can be 'past_due'
        // Adjust based on your actual subscription model structure
        $pastDueSubscriptions = Subscription::where('status', 'expired')
            ->where('ends_at', '>', now()->subDays(7)) // Expired in last 7 days
            ->with('tenant')
            ->get();

        foreach ($pastDueSubscriptions as $subscription) {
            if (!$subscription->tenant) {
                continue;
            }

            $alert = Alert::findOrCreateByType(
                Alert::TYPE_PAYMENT_RISK,
                $subscription->tenant_id,
                [
                    'severity' => Alert::SEVERITY_HIGH,
                    'title' => "Payment Risk: {$subscription->tenant->name}",
                    'message' => "Subscription has expired. Tenant may need payment assistance.",
                    'metadata' => [
                        'subscription_id' => $subscription->id,
                        'status' => $subscription->status,
                        'ends_at' => $subscription->ends_at?->toDateString(),
                    ],
                ]
            );

            if (!$alert->wasTriggeredRecently()) {
                $alert->updateLastTriggered();
                $this->sendAlertNotification($alert);
                $alertsTriggered++;
            }
        }

        return $alertsTriggered;
    }

    /**
     * Check for system health alerts
     * - Screen inactivity (no active screens for extended period)
     * Note: Repeated 500 errors would require logging infrastructure, skipped for lightweight implementation
     */
    private function checkSystemHealth(): int
    {
        $alertsTriggered = 0;

        // Check for screen inactivity (all tenants with no active screens for 7+ days)
        // This is a simplified check - in production you might want more sophisticated logic
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // Get tenants with subscriptions
        $tenantsWithSubscriptions = Tenant::where('is_active', true)
            ->whereHas('subscriptions', function ($query) {
                $query->where('status', Subscription::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
            })
            ->get();

        foreach ($tenantsWithSubscriptions as $tenant) {
            // Check if tenant has any active screens
            $activeScreens = ActiveScreen::where('tenant_id', $tenant->id)
                ->where('last_heartbeat_at', '>=', Carbon::now()->subSeconds(30))
                ->exists();

            if ($activeScreens) {
                continue; // Tenant has active screens
            }

            // Check last screen activity
            $lastScreen = ActiveScreen::where('tenant_id', $tenant->id)
                ->orderBy('last_heartbeat_at', 'desc')
                ->first();

            if ($lastScreen && $lastScreen->last_heartbeat_at->lessThan($sevenDaysAgo)) {
                $alert = Alert::findOrCreateByType(
                    Alert::TYPE_SYSTEM_HEALTH,
                    $tenant->id,
                    [
                        'severity' => Alert::SEVERITY_LOW,
                        'title' => "Screen Inactivity: {$tenant->name}",
                        'message' => "No active screens detected in the last 7 days. Tenant may not be using display screens.",
                        'metadata' => [
                            'days_inactive' => $lastScreen->last_heartbeat_at->diffInDays(now()),
                            'last_heartbeat' => $lastScreen->last_heartbeat_at->toDateTimeString(),
                        ],
                    ]
                );

                if (!$alert->wasTriggeredRecently()) {
                    $alert->updateLastTriggered();
                    $this->sendAlertNotification($alert);
                    $alertsTriggered++;
                }
            }
        }

        return $alertsTriggered;
    }

    /**
     * Send alert notification email to platform admin
     */
    private function sendAlertNotification(Alert $alert): void
    {
        try {
            // Get platform admin email (first super admin user)
            $adminUser = \App\Models\User::where('is_super_admin', true)->first();

            if (!$adminUser) {
                Log::warning('Alert notification skipped: No super admin user found');
                return;
            }

            // Send email notification
            Mail::raw($this->formatAlertEmail($alert), function ($message) use ($adminUser, $alert) {
                $message->to($adminUser->email)
                    ->subject("Alert: {$alert->title}");
            });

            Log::info('Alert notification sent', [
                'alert_id' => $alert->id,
                'type' => $alert->type,
                'tenant_id' => $alert->tenant_id,
                'recipient' => $adminUser->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send alert notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format alert email content
     */
    private function formatAlertEmail(Alert $alert): string
    {
        $tenantInfo = $alert->tenant ? "Tenant: {$alert->tenant->name} ({$alert->tenant->email})" : 'System-wide alert';
        
        return "Alert: {$alert->title}\n\n" .
               "Severity: " . strtoupper($alert->severity) . "\n" .
               "Type: {$alert->type}\n" .
               "{$tenantInfo}\n\n" .
               "Message:\n{$alert->message}\n\n" .
               "View alert: " . route('platform.alerts.show', $alert) . "\n";
    }
}
