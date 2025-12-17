<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\ActiveScreen;
use App\Models\ScreenUsageLog;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MetricsController extends Controller
{
    /**
     * Display the metrics dashboard
     * 
     * This dashboard shows system-wide metrics for investors/enterprise.
     * Only accessible to admin users and requires authentication.
     */
    public function index()
    {
        // Security: Only admins can access this dashboard
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access. Admin access required.');
        }

        $metrics = $this->calculateMetrics();

        return view('metrics.dashboard', compact('metrics'));
    }

    /**
     * Calculate all metrics (read-only queries)
     * 
     * Note: We bypass TenantScope to get system-wide metrics
     * 
     * @return array
     */
    private function calculateMetrics(): array
    {
        return [
            'total_tenants' => $this->getTotalTenants(),
            'active_tenants' => $this->getActiveTenants(),
            'active_paid_subscriptions' => $this->getActivePaidSubscriptions(),
            'monthly_recurring_revenue' => $this->getMonthlyRecurringRevenue(),
            'active_screens_today' => $this->getActiveScreensToday(),
            'total_screen_hours' => $this->getTotalScreenHours(),
            'usage_by_type' => $this->getUsageByType(),
            'subscription_breakdown' => $this->getSubscriptionBreakdown(),
            'tenants_by_status' => $this->getTenantsByStatus(),
        ];
    }

    /**
     * Get total number of tenants (including inactive and soft-deleted)
     * 
     * Note: Tenant model doesn't have TenantScope (it IS the tenant),
     * but we use withoutGlobalScope to ensure no scoping is applied
     * 
     * @return int
     */
    private function getTotalTenants(): int
    {
        // Tenant model itself doesn't typically have tenant scoping, but ensure we get all
        return Tenant::withTrashed()->count();
    }

    /**
     * Get active tenants (last 30 days)
     * 
     * Active tenants are those that have:
     * - Is_active = true
     * - Have activity in the last 30 days (screen usage, subscription activity, etc.)
     * 
     * @return int
     */
    private function getActiveTenants(): int
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Get tenants that have activity (screen usage, subscription, or user activity) in last 30 days
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
            ->unique()
            ->toArray();

        return Tenant::withTrashed()
            ->where('is_active', true)
            ->whereIn('id', $tenantIds)
            ->count();
    }

    /**
     * Get count of active paid subscriptions
     * 
     * Active paid subscriptions are those that:
     * - Status = 'active'
     * - Have an associated plan with price > 0
     * - Are not expired (ends_at is null or in the future)
     * 
     * @return int
     */
    private function getActivePaidSubscriptions(): int
    {
        // Subscription model doesn't have TenantScope, so we can query directly
        return Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->whereHas('plan', function ($query) {
                $query->where('price', '>', 0);
            })
            ->count();
    }

    /**
     * Calculate Monthly Recurring Revenue (MRR)
     * 
     * MRR is calculated as the sum of all active subscription plan prices.
     * For annual plans, we calculate monthly equivalent (price / 12).
     * 
     * Note: This assumes billing is subscription-based. If billing is manual,
     * this metric may not reflect actual revenue.
     * 
     * @return float
     */
    private function getMonthlyRecurringRevenue(): float
    {
        // Subscription model doesn't have TenantScope, so we can query directly
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
     * Get count of active screens today
     * 
     * Active screens are those that have sent a heartbeat in the last 30 seconds
     * (screens that are currently displaying)
     * 
     * @return int
     */
    private function getActiveScreensToday(): int
    {
        return ActiveScreen::withoutGlobalScope(TenantScope::class)
            ->where('last_heartbeat_at', '>=', Carbon::now()->subSeconds(30))
            ->count();
    }

    /**
     * Get total screen usage hours (all time)
     * 
     * Calculated from screen_usage_logs where ended_at is not null
     * (completed sessions only)
     * 
     * @return float
     */
    private function getTotalScreenHours(): float
    {
        $totalSeconds = ScreenUsageLog::withoutGlobalScope(TenantScope::class)
            ->whereNotNull('ended_at')
            ->whereNotNull('duration_seconds')
            ->sum('duration_seconds');

        return round($totalSeconds / 3600, 2);
    }

    /**
     * Get usage breakdown by screen type (Queue vs Service)
     * 
     * Returns hours used for each screen type (all time)
     * Note: 'service' replaces the old 'opd_lab' screen type
     * 
     * @return array ['queue' => float, 'service' => float]
     */
    private function getUsageByType(): array
    {
        $breakdown = ScreenUsageLog::withoutGlobalScope(TenantScope::class)
            ->whereNotNull('ended_at')
            ->whereNotNull('duration_seconds')
            ->selectRaw('screen_type, SUM(duration_seconds) as total_seconds')
            ->groupBy('screen_type')
            ->pluck('total_seconds', 'screen_type')
            ->toArray();

        // Combine 'opd_lab' (legacy) with 'service' (new) for backward compatibility
        $serviceHours = round((($breakdown['service'] ?? 0) + ($breakdown['opd_lab'] ?? 0)) / 3600, 2);

        return [
            'queue' => round(($breakdown['queue'] ?? 0) / 3600, 2),
            'service' => $serviceHours,
        ];
    }

    /**
     * Get subscription breakdown by plan
     * 
     * Returns count of active subscriptions grouped by plan
     * 
     * @return array
     */
    private function getSubscriptionBreakdown(): array
    {
        // Subscription model doesn't have TenantScope, so we can query directly
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
     * Get tenant count by status
     * 
     * @return array
     */
    private function getTenantsByStatus(): array
    {
        // Tenant model doesn't have TenantScope, use withTrashed to include all
        $tenants = Tenant::withTrashed()
            ->select('is_active', DB::raw('count(*) as count'))
            ->groupBy('is_active')
            ->pluck('count', 'is_active')
            ->toArray();

        return [
            'active' => $tenants[1] ?? 0,
            'inactive' => $tenants[0] ?? 0,
        ];
    }
}
