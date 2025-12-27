<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    /**
     * Display a listing of alerts (Super Admin only)
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access alerts.');
        }

        $query = Alert::with('tenant')->latest('last_triggered_at');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'resolved') {
                $query->whereNotNull('resolved_at');
            } elseif ($request->status === 'active') {
                $query->whereNull('resolved_at');
            }
        }

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->ofSeverity($request->severity);
        }

        $alerts = $query->paginate(20);

        return view('platform.alerts.index', compact('alerts'));
    }

    /**
     * Display the specified alert
     */
    public function show(Alert $alert)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access alerts.');
        }

        $alert->load('tenant');

        return view('platform.alerts.show', compact('alert'));
    }

    /**
     * Mark alert as resolved
     */
    public function resolve(Alert $alert)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can resolve alerts.');
        }

        $alert->markAsResolved();

        return redirect()->route('platform.alerts.index')
            ->with('success', 'Alert marked as resolved.');
    }

    /**
     * Mark alert as unresolved
     */
    public function unresolve(Alert $alert)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can manage alerts.');
        }

        $alert->markAsUnresolved();

        return redirect()->route('platform.alerts.index')
            ->with('success', 'Alert marked as active.');
    }
}
