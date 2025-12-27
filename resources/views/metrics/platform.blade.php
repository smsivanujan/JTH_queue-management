@extends('layouts.platform')

@section('title', 'Platform Metrics - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .metric-card {
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    .metric-card:nth-child(1) { animation-delay: 0.1s; }
    .metric-card:nth-child(2) { animation-delay: 0.2s; }
    .metric-card:nth-child(3) { animation-delay: 0.3s; }
    .metric-card:nth-child(4) { animation-delay: 0.4s; }
    .metric-card:nth-child(5) { animation-delay: 0.5s; }
    .metric-card:nth-child(6) { animation-delay: 0.6s; }
    .metric-card:nth-child(7) { animation-delay: 0.7s; }
    .metric-card:nth-child(8) { animation-delay: 0.8s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">
                            Platform Metrics
                        </h1>
                        <p class="text-sm text-gray-500">System-wide analytics and business intelligence</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('platform.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Tenants -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Tenants</h3>
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['total_tenants']) }}</div>
                <p class="text-xs text-gray-500">All registered organizations</p>
            </div>

            <!-- Active Tenants (7 days) -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Tenants</h3>
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['active_tenants_7d']) }}</div>
                <p class="text-xs text-gray-500">Active in last 7 days</p>
                <p class="text-xs text-gray-400 mt-1">({{ number_format($metrics['active_tenants_30d']) }} in last 30 days)</p>
            </div>

            <!-- Queues Opened Today -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Queues Opened</h3>
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['queues_opened_today']) }}</div>
                <p class="text-xs text-gray-500">Today</p>
                <p class="text-xs text-gray-400 mt-1">({{ number_format($metrics['queues_opened_week']) }} this week)</p>
            </div>

            <!-- Active Screens Today -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Screens</h3>
                    <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['active_screens_today']) }}</div>
                <p class="text-xs text-gray-500">Currently displaying</p>
            </div>
        </div>

        <!-- Revenue & Conversion Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Recurring Revenue -->
            <div class="metric-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-white/90 uppercase tracking-wider">Monthly Recurring Revenue</h3>
                    <svg class="w-6 h-6 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold mb-2">${{ number_format($metrics['monthly_recurring_revenue'], 2) }}</div>
                <p class="text-xs text-white/80">MRR from active subscriptions</p>
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-xs text-white/70">Based on plan prices. Actual revenue may vary with manual billing.</p>
                </div>
            </div>

            <!-- Trial to Paid Conversion -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Trial Conversion</h3>
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['trial_to_paid_conversion']['conversion_rate'], 1) }}%</div>
                <p class="text-xs text-gray-500 mb-2">
                    {{ number_format($metrics['trial_to_paid_conversion']['converted']) }} of {{ number_format($metrics['trial_to_paid_conversion']['total_trials']) }} trials converted
                </p>
                @if($metrics['trial_to_paid_conversion']['total_trials'] > 0)
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $metrics['trial_to_paid_conversion']['conversion_rate'] }}%"></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscription Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Subscriptions by Plan</h3>
            @if(count($metrics['subscription_breakdown']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($metrics['subscription_breakdown'] as $planName => $count)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-sm font-medium text-gray-700">{{ $planName }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No active subscriptions</p>
            @endif
        </div>

        <!-- Important Notes -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>All metrics are calculated in real-time and reflect current system state</li>
                            <li>MRR is based on plan prices; actual revenue may vary with manual billing arrangements</li>
                            <li>Active tenants are defined as those with activity (screens, subscriptions, queues) in the specified period</li>
                            <li>Queues opened = queues created/accessed (created on-demand when first accessed)</li>
                            <li>This dashboard is read-only and does not expose personal data</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
