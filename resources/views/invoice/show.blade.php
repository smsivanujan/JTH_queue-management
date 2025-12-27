@extends('layouts.tenant')

@section('title', 'Invoice ' . $invoice->invoice_number . ' - SmartQueue')

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
    <div class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Invoice details and receipt</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('app.invoices.download', $invoice) }}" target="_blank" class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        Download PDF
                    </a>
                    <a href="{{ route('app.invoices.index') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        Back to Invoices
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="bg-white rounded-xl shadow-lg border-2 border-gray-200 p-6 sm:p-8 lg:p-12">
            <!-- Invoice Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-8 border-b border-gray-200">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Invoice</h2>
                    <p class="text-sm text-gray-600">Invoice #{{ $invoice->invoice_number }}</p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    @if($invoice->status === 'paid')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Paid
                        </span>
                    @elseif($invoice->status === 'pending')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            Pending Payment
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                            Cancelled
                        </span>
                    @endif
                </div>
            </div>

            <!-- Company and Tenant Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">From</h3>
                    <div class="text-sm text-gray-900">
                        <p class="font-semibold">SmartQueue Inc.</p>
                        <p class="mt-1 text-gray-600">Queue Management System</p>
                        <p class="mt-1 text-gray-600">support@smartqueue.com</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">To</h3>
                    <div class="text-sm text-gray-900">
                        <p class="font-semibold">{{ $tenant->name }}</p>
                        @if($tenant->email)
                            <p class="mt-1 text-gray-600">{{ $tenant->email }}</p>
                        @endif
                        @if($tenant->address)
                            <p class="mt-1 text-gray-600">{{ $tenant->address }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Issue Date</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $invoice->issued_at->format('F d, Y') }}</p>
                        </div>
                        @if($invoice->paid_at)
                            <div>
                                <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Paid Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $invoice->paid_at->format('F d, Y') }}</p>
                            </div>
                        @endif
                        @if($invoice->payment_method)
                            <div>
                                <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Payment Method</p>
                                <p class="text-sm font-semibold text-gray-900">{{ ucfirst($invoice->payment_method) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-8">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $invoice->metadata['plan_name'] ?? 'Subscription' }}</p>
                                    @if(isset($invoice->metadata['billing_cycle']))
                                        <p class="text-xs text-gray-600 mt-1">{{ ucfirst($invoice->metadata['billing_cycle']) }} subscription</p>
                                    @endif
                                    @if(isset($invoice->metadata['subscription_starts_at']) && isset($invoice->metadata['subscription_ends_at']))
                                        <p class="text-xs text-gray-600 mt-1">
                                            {{ \Carbon\Carbon::parse($invoice->metadata['subscription_starts_at'])->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($invoice->metadata['subscription_ends_at'])->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ number_format($invoice->amount, 2) }}</p>
                                <p class="text-xs text-gray-600">{{ $invoice->currency }}</p>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-4 py-4 text-right font-semibold text-gray-900" colspan="2">
                                <div class="flex items-center justify-end gap-4">
                                    <span class="text-sm uppercase text-gray-600">Total:</span>
                                    <span class="text-xl">${{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Notes -->
            @if($invoice->status === 'paid')
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-green-900">Payment Received</p>
                                <p class="text-xs text-green-800 mt-1">This invoice has been paid. Thank you for your business!</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($invoice->status === 'pending')
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-900">Payment Pending</p>
                                <p class="text-xs text-amber-800 mt-1">This invoice is awaiting payment. Please complete payment to activate your subscription.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

