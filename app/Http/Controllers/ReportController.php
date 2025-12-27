<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Service;
use App\Models\Queue;
use App\Models\SubQueue;
use App\Models\Invoice;
use App\Models\ActiveScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show the tenant reports page
     */
    public function index(Request $request)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        // Get selected month/year or default to current month
        $selectedMonth = $request->input('month', date('Y-m'));
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Calculate usage metrics for the month
        $metrics = $this->calculateMonthlyMetrics($tenant, $monthStart, $monthEnd);

        // Get invoices for the month
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.index', compact('tenant', 'metrics', 'invoices', 'selectedMonth', 'monthStart', 'monthEnd'));
    }

    /**
     * Download tenant report as PDF
     */
    public function download(Request $request)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            abort(403, 'Tenant not identified');
        }

        // Get selected month/year or default to current month
        $selectedMonth = $request->input('month', date('Y-m'));
        $monthStart = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Calculate usage metrics for the month
        $metrics = $this->calculateMonthlyMetrics($tenant, $monthStart, $monthEnd);

        // Get invoices for the month
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate PDF HTML
        $html = view('reports.pdf', compact('tenant', 'metrics', 'invoices', 'monthStart', 'monthEnd'))->render();

        // Return HTML for now (can be converted to PDF using DomPDF or similar)
        return response()->make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "inline; filename=\"report-{$selectedMonth}.html\"",
        ]);
    }

    /**
     * Calculate monthly metrics for a tenant
     */
    private function calculateMonthlyMetrics($tenant, $monthStart, $monthEnd): array
    {
        // Total clinics (at end of month)
        $clinicsCount = Clinic::where('tenant_id', $tenant->id)
            ->where('created_at', '<=', $monthEnd)
            ->count();

        // Total services (at end of month)
        $servicesCount = Service::where('tenant_id', $tenant->id)
            ->where('created_at', '<=', $monthEnd)
            ->count();

        // Queues opened in the month
        $queuesOpened = Queue::where('tenant_id', $tenant->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        // Tokens served in the month (sum of current_number - 1 from SubQueues created/updated in the month)
        $tokensServed = SubQueue::where('tenant_id', $tenant->id)
            ->where(function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->orWhereBetween('updated_at', [$monthStart, $monthEnd]);
            })
            ->sum(\DB::raw('GREATEST(current_number - 1, 0)'));

        // Active screens count (screens that had activity in the month)
        $screensUsed = ActiveScreen::where('tenant_id', $tenant->id)
            ->where(function ($query) use ($monthStart, $monthEnd) {
                $query->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->orWhereBetween('last_heartbeat_at', [$monthStart, $monthEnd]);
            })
            ->distinct('screen_token')
            ->count('screen_token');

        return [
            'clinics_count' => $clinicsCount,
            'services_count' => $servicesCount,
            'queues_opened' => $queuesOpened,
            'tokens_served' => (int) $tokensServed,
            'screens_used' => $screensUsed,
        ];
    }
}
