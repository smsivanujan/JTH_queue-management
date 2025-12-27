@extends('layouts.tenant')

@section('title', 'Monthly Reports - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Monthly Reports</h1>
                    <p class="text-sm text-gray-500 mt-1">View your organization's monthly usage and billing summary</p>
                </div>
                <a href="{{ route('app.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Month Selector -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('app.reports.index') }}" class="flex items-center gap-4">
                <label for="month" class="text-sm font-medium text-gray-700">Select Month:</label>
                <input 
                    type="month" 
                    id="month" 
                    name="month" 
                    value="{{ $selectedMonth }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm">
                    View Report
                </button>
                <a href="{{ route('app.reports.download', ['month' => $selectedMonth]) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-sm ml-auto">
                    Download PDF
                </a>
            </form>
        </div>

        <!-- Usage Summary -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Usage Summary - {{ $monthStart->format('F Y') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $metrics['clinics_count'] }}</div>
                    <div class="text-sm text-gray-600">Clinics</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $metrics['services_count'] }}</div>
                    <div class="text-sm text-gray-600">Services</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $metrics['queues_opened'] }}</div>
                    <div class="text-sm text-gray-600">Queues Opened</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-pink-600 mb-2">{{ number_format($metrics['tokens_served']) }}</div>
                    <div class="text-sm text-gray-600">Tokens Served</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-red-600 mb-2">{{ $metrics['screens_used'] }}</div>
                    <div class="text-sm text-gray-600">Screens Used</div>
                </div>
            </div>
        </div>

        <!-- Billing Summary -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Billing Summary - {{ $monthStart->format('F Y') }}</h2>
            
            @if($invoices->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-600">No invoices found for this month.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Invoice #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Payment Method</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ $invoice->invoice_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->created_at->format('M j, Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${{ number_format($invoice->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($invoice->isPaid())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                                Paid
                                            </span>
                                        @elseif($invoice->isPending())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ ucfirst($invoice->payment_method ?? 'N/A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <a href="{{ route('app.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-900">Total</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900">${{ number_format($invoices->sum('amount'), 2) }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

