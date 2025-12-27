<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Show the support contact form
     */
    public function index()
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        return view('support.index', compact('tenant'));
    }

    /**
     * Store a new support ticket
     */
    public function store(Request $request)
    {
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        
        if (!$tenant) {
            return redirect()->route('tenant.select')
                ->withErrors(['Please select an organization.']);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:general,technical,billing,feature_request'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
        ]);

        try {
            // Determine priority if not provided
            $priority = $validated['priority'] ?? SupportTicket::PRIORITY_NORMAL;

            // Create the support ticket
            $ticket = SupportTicket::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'category' => $validated['category'],
                'message' => $validated['message'],
                'priority' => $priority,
                'status' => SupportTicket::STATUS_OPEN,
            ]);

            // Send email notification to platform admin
            try {
                $adminUser = \App\Models\User::where('is_super_admin', true)->first();
                
                if ($adminUser) {
                    Mail::to($adminUser->email)->send(new \App\Mail\TicketSubmitted($ticket));
                }
            } catch (\Exception $e) {
                // Log email failure but don't fail the ticket creation
                Log::warning('Failed to send ticket notification email', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('app.support.index')
                ->with('success', 'Your support ticket has been submitted successfully. We\'ll get back to you soon!');
        } catch (\Exception $e) {
            Log::error('Failed to create support ticket', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit support ticket. Please try again.']);
        }
    }
}
