<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\ActiveScreen;
use App\Models\ScreenUsageLog;
use App\Models\Queue;
use App\Models\SubQueue;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetricsController extends Controller
{
    /**
     * Display platform metrics dashboard (Super Admin only)
     */
    public function platformIndex()
    {
        // Only Super Admin can access platform metrics
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access. Super Admin access required.');
        }

        $metrics = $this->calculatePlatformMetrics();

        return view('metrics.platform', compact('metrics'));
    }

    /**
     * Display tenant metrics dashboard
     */
    public function tenantIndex()
    {
        $tenant = app('tenant');
        
        if (!$tenant) {
            abort(403, 'Tenant context required.');
        }

        $metrics = $this->calculateTenantMetrics($tenant);

        return view('metrics.tenant', compact('metrics', 'tenant'));
    }

    /**
     * Calculate platform metrics (read-only queries)
     */
    private function calculatePlatformMetrics(): array
    {
        return [
            'total_tenants' => $this->getTotalTenants(),
            'active_tenants_7d' => $this->getActiveTenantsLast7Days(),
            'active_tenants_30d' => $this->getActiveTenantsLast30Days(),
            'queues_opened_today' => $this->getQueuesOpenedToday(),
            'queues_opened_week' => $this->getQueuesOpenedThisWeek(),
            'active_screens_today' => $this->getActiveScreensToday(),
            'trial_to_paid_conversion' => $this->getTrialToPaidConversion(),
            'monthly_recurring_revenue' => $this->getMonthlyRecurringRevenue(),
            'subscription_breakdown' => $this->getSubscriptionBreakdown(),
        ];
    }

    /**
     * Calculate tenant metrics (read-only queries)
     */
    private function calculateTenantMetrics(Tenant $tenant): array
    {
        return [
            'queues_opened_today' => $this->getTenantQueuesOpenedToday($tenant),
            'queues_opened_week' => $this->getTenantQueuesOpenedThisWeek($tenant),
            'tokens_served_today' => $this->getTenantTokensServedToday($tenant),
            'tokens_served_week' => $this->getTenantTokensServedThisWeek($tenant),
            'active_screens' => $this->getTenantActiveScreens($tenant),
            'usage_trends' => $this->getTenantUsageTrends($tenant),
        ];
    }

    /**
     * Get total number of tenants
     */
    private function getTotalTenants(): int
    {
        return Tenant::withTrashed()->count();
    }

    /**
     * Get active tenants (last 7 days)
     * Active = has activity (screen usage, subscription activity, queue access)
     */
    private function getActiveTenantsLast7Days(): int
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $tenantIds = DB::table('screen_usage_logs')
            ->where('started_at', '>=', $sevenDaysAgo)
            ->distinct()
            ->pluck('tenant_id')
            ->merge(
                DB::table('subscriptions')
                    ->where('updated_at', '>=', $sevenDaysAgo)
                    ->distinct()
                    ->pluck('tenant_id')
            )
            ->merge(
                DB::table('queues')
                    ->where('created_at', '>=', $sevenDaysAgo)
                    ->distinct()
                    ->pluck('tenant_id')
            )
            ->unique()
            ->toArray();

        return Tenant::withTrashed()
            ->where('is_active', true)
            ->whereIn('id', $tenantIds)
            ->count();
    }

    /**
     * Get active tenants (last 30 days) - for comparison
     */
    private function getActiveTenantsLast30Days(): int
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $tenantIds = DB::table('screen_usage_logs')
            ->where('started_at', '>=', $thirtyDaysAgo)
            ->distinct()
            ->pluck('tenant_id')
            ->merge(
                DB::table('subscriptions')
                    ->where('updated_at', '>=', $thirtyDaysAgo)
                    ->distinct()
                    ->pluck('tenant_id')
            )
            ->merge(
                DB::table('queues')
                    ->where('created_at', '>=', $thirtyDaysAgo)
                    ->distinct()
                    ->pluck('tenant_id')
            )
            ->unique()
            ->toArray();

        return Tenant::withTrashed()
            ->where('is_active', true)
            ->whereIn('id', $tenantIds)
            ->count();
    }

    /**
     * Get queues opened today (platform-wide)
     * Queues are created on-demand when accessed, so created_at tracks "opened"
     */
    private function getQueuesOpenedToday(): int
    {
        return Queue::withoutGlobalScope(TenantScope::class)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get queues opened this week (platform-wide)
     */
    private function getQueuesOpenedThisWeek(): int
    {
        return Queue::withoutGlobalScope(TenantScope::class)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();
    }

    /**
     * Get count of active screens today (platform-wide)
     */
    private function getActiveScreensToday(): int
    {
        return ActiveScreen::withoutGlobalScope(TenantScope::class)
            ->where('last_heartbeat_at', '>=', Carbon::now()->subSeconds(30))
            ->count();
    }

    /**
     * Calculate trial to paid conversion rate
     * Conversion = tenants who had trial subscription and now have paid subscription
     */
    private function getTrialToPaidConversion(): array
    {
        // Get all tenants who ever had a trial subscription
        $trialTenants = Subscription::where('status', Subscription::STATUS_TRIAL)
            ->orWhere('plan_name', 'trial')
            ->distinct()
            ->pluck('tenant_id')
            ->toArray();

        $totalTrials = count($trialTenants);

        if ($totalTrials === 0) {
            return [
                'total_trials' => 0,
                'converted' => 0,
                'conversion_rate' => 0,
            ];
        }

        // Get tenants who have active paid subscriptions (price > 0)
        $convertedTenants = Subscription::whereIn('tenant_id', $trialTenants)
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->whereHas('plan', function ($query) {
                $query->where('price', '>', 0);
            })
            ->distinct()
            ->pluck('tenant_id')
            ->count();

        $conversionRate = round(($convertedTenants / $totalTrials) * 100, 1);

        return [
            'total_trials' => $totalTrials,
            'converted' => $convertedTenants,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Calculate Monthly Recurring Revenue (MRR)
     */
    private function getMonthlyRecurringRevenue(): float
    {
        $subscriptions = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->get();

        $mrr = 0;

        foreach ($subscriptions as $subscription) {
            if (!$subscription->plan || $subscription->plan->price <= 0) {
                continue;
            }

            $planPrice = (float) $subscription->plan->price;
            $billingCycle = $subscription->plan->billing_cycle ?? 'monthly';

            // Convert to monthly equivalent
            switch (strtolower($billingCycle)) {
                case 'yearly':
                case 'annual':
                    $mrr += $planPrice / 12;
                    break;
                case 'monthly':
                default:
                    $mrr += $planPrice;
                    break;
            }
        }

        return round($mrr, 2);
    }

    /**
     * Get subscription breakdown by plan
     */
    private function getSubscriptionBreakdown(): array
    {
        return Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->get()
            ->groupBy(function ($subscription) {
                return $subscription->plan ? $subscription->plan->name : 'Unknown Plan';
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
    }

    /**
     * Get queues opened today for tenant
     */
    private function getTenantQueuesOpenedToday(Tenant $tenant): int
    {
        return Queue::where('tenant_id', $tenant->id)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get queues opened this week for tenant
     */
    private function getTenantQueuesOpenedThisWeek(Tenant $tenant): int
    {
        return Queue::where('tenant_id', $tenant->id)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();
    }

    /**
     * Get tokens served today for tenant
     * Tokens = number of times "next" was called (current_number - 1 per sub-queue)
     * We count sub-queues that have been advanced (current_number > 1)
     */
    private function getTenantTokensServedToday(Tenant $tenant): int
    {
        // Count tokens served = sum of (current_number - 1) for all active sub-queues
        // current_number starts at 1, so if it's 5, that means 4 tokens were served (2,3,4,5)
        // Actually, if current_number is 5, we're displaying token 5, so 5 tokens have been shown
        // So we sum current_number for all sub-queues that have been used
        return SubQueue::where('tenant_id', $tenant->id)
            ->where('current_number', '>', 1)
            ->whereDate('updated_at', today())
            ->sum(DB::raw('current_number - 1'));
    }

    /**
     * Get tokens served this week for tenant
     */
    private function getTenantTokensServedThisWeek(Tenant $tenant): int
    {
        return SubQueue::where('tenant_id', $tenant->id)
            ->where('current_number', '>', 1)
            ->where('updated_at', '>=', Carbon::now()->startOfWeek())
            ->sum(DB::raw('current_number - 1'));
    }

    /**
     * Get active screens for tenant
     */
    private function getTenantActiveScreens(Tenant $tenant): int
    {
        return ActiveScreen::getActiveCount($tenant->id, null, 30);
    }

    /**
     * Get usage trends for tenant (last 7 days)
     */
    private function getTenantUsageTrends(Tenant $tenant): array
    {
        $trends = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStart = $date->copy()->startOfDay();
            $dateEnd = $date->copy()->endOfDay();

            $trends[] = [
                'date' => $date->format('M d'),
                'queues' => Queue::where('tenant_id', $tenant->id)
                    ->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->count(),
                'tokens' => SubQueue::where('tenant_id', $tenant->id)
                    ->where('current_number', '>', 1)
                    ->whereBetween('updated_at', [$dateStart, $dateEnd])
                    ->sum(DB::raw('current_number - 1')),
            ];
        }

        return $trends;
    }
}
