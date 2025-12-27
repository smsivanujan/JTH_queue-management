<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Display a listing of support tickets (Super Admin only)
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access support tickets.');
        }

        $query = SupportTicket::with(['tenant', 'user'])->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->ofStatus($request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->ofCategory($request->category);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->ofPriority($request->priority);
        }

        $tickets = $query->paginate(20);

        return view('platform.support.index', compact('tickets'));
    }

    /**
     * Display the specified support ticket
     */
    public function show(SupportTicket $supportTicket)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can access support tickets.');
        }

        $supportTicket->load(['tenant', 'user']);

        return view('platform.support.show', compact('supportTicket'));
    }

    /**
     * Mark ticket as replied
     */
    public function markReplied(Request $request, SupportTicket $supportTicket)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can manage support tickets.');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $supportTicket->update([
            'admin_notes' => $validated['admin_notes'] ?? $supportTicket->admin_notes,
        ]);

        $supportTicket->markAsReplied();

        return redirect()->route('platform.support.show', $supportTicket)
            ->with('success', 'Ticket marked as replied.');
    }

    /**
     * Mark ticket as closed
     */
    public function markClosed(Request $request, SupportTicket $supportTicket)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can manage support tickets.');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $supportTicket->update([
            'admin_notes' => $validated['admin_notes'] ?? $supportTicket->admin_notes,
        ]);

        $supportTicket->markAsClosed();

        return redirect()->route('platform.support.show', $supportTicket)
            ->with('success', 'Ticket marked as closed.');
    }

    /**
     * Reopen ticket
     */
    public function reopen(SupportTicket $supportTicket)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only platform administrators can manage support tickets.');
        }

        $supportTicket->reopen();

        return redirect()->route('platform.support.show', $supportTicket)
            ->with('success', 'Ticket reopened.');
    }
}
