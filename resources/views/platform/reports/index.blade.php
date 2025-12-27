@extends('layouts.platform')

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
                    <h1 class="text-2xl font-bold text-gray-900">Monthly Business Reports</h1>
                    <p class="text-sm text-gray-500 mt-1">View platform-wide monthly business metrics</p>
                </div>
                <a href="{{ route('platform.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Month Selector -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('platform.reports.index') }}" class="flex items-center gap-4">
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
                <a href="{{ route('platform.reports.download', ['month' => $selectedMonth]) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-sm ml-auto">
                    Download PDF
                </a>
            </form>
        </div>

        <!-- Business Summary -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Business Summary - {{ $monthStart->format('F Y') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $metrics['total_tenants'] }}</div>
                    <div class="text-sm font-medium text-blue-800">Total Tenants</div>
                    <div class="text-xs text-blue-600 mt-1">As of month end</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200">
                    <div class="text-4xl font-bold text-green-600 mb-2">{{ $metrics['active_tenants'] }}</div>
                    <div class="text-sm font-medium text-green-800">Active Tenants</div>
                    <div class="text-xs text-green-600 mt-1">With active subscriptions</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <div class="text-4xl font-bold text-purple-600 mb-2">{{ $metrics['new_tenants'] }}</div>
                    <div class="text-sm font-medium text-purple-800">New Tenants</div>
                    <div class="text-xs text-purple-600 mt-1">Created this month</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                    <div class="text-4xl font-bold text-orange-600 mb-2">{{ $metrics['trial_conversions'] }}</div>
                    <div class="text-sm font-medium text-orange-800">Trial Conversions</div>
                    <div class="text-xs text-orange-600 mt-1">Trial → Paid</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200">
                    <div class="text-4xl font-bold text-indigo-600 mb-2">${{ number_format($metrics['mrr'], 0) }}</div>
                    <div class="text-sm font-medium text-indigo-800">Monthly MRR</div>
                    <div class="text-xs text-indigo-600 mt-1">Recurring revenue</div>
                </div>
            </div>

            <!-- Conversion Rate -->
            @if($metrics['total_tenants'] > 0)
                <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Metrics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Active Rate</div>
                            <div class="text-3xl font-bold text-gray-900">
                                {{ $metrics['total_tenants'] > 0 ? number_format(($metrics['active_tenants'] / $metrics['total_tenants']) * 100, 1) : 0 }}%
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $metrics['active_tenants'] }} of {{ $metrics['total_tenants'] }} tenants</div>
                        </div>
                        @if($metrics['new_tenants'] > 0)
                            <div>
                                <div class="text-sm text-gray-600 mb-1">Conversion Rate (New Tenants)</div>
                                <div class="text-3xl font-bold text-gray-900">
                                    {{ number_format(($metrics['trial_conversions'] / $metrics['new_tenants']) * 100, 1) }}%
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $metrics['trial_conversions'] }} conversions from {{ $metrics['new_tenants'] }} new tenants</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

