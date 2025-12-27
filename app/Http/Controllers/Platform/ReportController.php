<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the platform reports page
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access reports.');
        }

        // Get selected month/year or default to current month
        $selectedMonth = $request->input('month', date('Y-m'));
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Calculate business metrics for the month
        $metrics = $this->calculateMonthlyMetrics($monthStart, $monthEnd);

        return view('platform.reports.index', compact('metrics', 'selectedMonth', 'monthStart', 'monthEnd'));
    }

    /**
     * Download platform report as PDF
     */
    public function download(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access reports.');
        }

        // Get selected month/year or default to current month
        $selectedMonth = $request->input('month', date('Y-m'));
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Calculate business metrics for the month
        $metrics = $this->calculateMonthlyMetrics($monthStart, $monthEnd);

        // Generate PDF HTML
        $html = view('platform.reports.pdf', compact('metrics', 'monthStart', 'monthEnd'))->render();

        // Return HTML for now (can be converted to PDF using DomPDF or similar)
        return response()->make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "inline; filename=\"platform-report-{$selectedMonth}.html\"",
        ]);
    }

    /**
     * Calculate monthly business metrics
     */
    private function calculateMonthlyMetrics($monthStart, $monthEnd): array
    {
        // Total tenants (at end of month)
        $totalTenants = Tenant::where('created_at', '<=', $monthEnd)
            ->where('is_active', true)
            ->count();

        // Active tenants (tenants with active subscription at end of month)
        $activeTenants = Tenant::whereHas('subscriptions', function ($query) use ($monthEnd) {
            $query->where('status', Subscription::STATUS_ACTIVE)
                ->where(function ($q) use ($monthEnd) {
                    $q->whereNull('ends_at')
                        ->orWhere('ends_at', '>', $monthEnd);
                });
        })
        ->where('created_at', '<=', $monthEnd)
        ->where('is_active', true)
        ->count();

        // New tenants created in the month
        $newTenants = Tenant::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('is_active', true)
            ->count();

        // Trial → paid conversions in the month
        // Count tenants that started trial before month start and got an active subscription in the month
        $trialConversions = Tenant::where('trial_ends_at', '<=', $monthEnd)
            ->where('created_at', '<', $monthStart)
            ->whereHas('subscriptions', function ($query) use ($monthStart, $monthEnd) {
                $query->where('status', Subscription::STATUS_ACTIVE)
                    ->whereBetween('created_at', [$monthStart, $monthEnd]);
            })
            ->where('is_active', true)
            ->count();

        // Simple MRR (Monthly Recurring Revenue) - sum of active subscription prices at end of month
        $mrr = Subscription::where('status', Subscription::STATUS_ACTIVE)
            ->where(function ($query) use ($monthEnd) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $monthEnd);
            })
            ->with('plan')
            ->get()
            ->sum(function ($subscription) {
                // Only count monthly subscriptions for MRR, or divide annual by 12
                $plan = $subscription->plan;
                if (!$plan) {
                    return 0;
                }

                // If plan has billing_cycle, use it; otherwise assume monthly
                $billingCycle = $plan->billing_cycle ?? 'monthly';
                
                if ($billingCycle === 'monthly') {
                    return $plan->price;
                } elseif ($billingCycle === 'yearly') {
                    return $plan->price / 12; // Convert annual to monthly
                }
                
                return 0;
            });

        return [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'new_tenants' => $newTenants,
            'trial_conversions' => $trialConversions,
            'mrr' => round($mrr, 2),
        ];
    }
}
