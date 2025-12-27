<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for the current tenant
     */
    public function index()
    {
        $tenant = app('tenant');
        
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->latest('issued_at')
            ->paginate(15);

        return view('invoice.index', compact('invoices'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $tenant = app('tenant');
        
        // Ensure invoice belongs to current tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        return view('invoice.show', compact('invoice', 'tenant'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        $tenant = app('tenant');
        
        // Ensure invoice belongs to current tenant
        if ($invoice->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Simple HTML to PDF using browser print
        // For production, use a library like dompdf/barryvdh-laravel-dompdf
        $html = view('invoice.pdf', compact('invoice', 'tenant'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $invoice->invoice_number . '.html"');
    }

    /**
     * Download invoice as PDF (for admin)
     */
    public function adminDownload(Invoice $invoice)
    {
        // Super Admin can download any invoice
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $tenant = $invoice->tenant;
        
        $html = view('invoice.pdf', compact('invoice', 'tenant'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $invoice->invoice_number . '.html"');
    }
}
