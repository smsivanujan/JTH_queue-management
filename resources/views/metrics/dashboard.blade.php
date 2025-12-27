@extends('layouts.platform')

@section('title', 'Enterprise Metrics - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .header_section {
        display: none;
    }
    .footer_section {
        display: none;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .metric-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
                            Enterprise Metrics Dashboard
                        </h1>
                        <p class="text-sm text-gray-500">System-wide analytics and business intelligence</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('platform.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                        Back to Platform Dashboard
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Total Tenants -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Tenants</h3>
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['total_tenants']) }}</div>
                <p class="text-xs text-gray-500">All registered organizations</p>
            </div>

            <!-- Active Tenants -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Tenants</h3>
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['active_tenants']) }}</div>
                <p class="text-xs text-gray-500">Active in last 30 days</p>
            </div>

            <!-- Active Paid Subscriptions -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Subscriptions</h3>
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['active_paid_subscriptions']) }}</div>
                <p class="text-xs text-gray-500">Paid active subscriptions</p>
            </div>

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
                    <p class="text-xs text-white/70">Note: Based on plan prices. Actual revenue may vary with manual billing.</p>
                </div>
            </div>

            <!-- Active Screens Today -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Screens Today</h3>
                    <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['active_screens_today']) }}</div>
                <p class="text-xs text-gray-500">Currently displaying</p>
            </div>

            <!-- Total Screen Hours -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Screen Hours</h3>
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($metrics['total_screen_hours'], 0) }}</div>
                <p class="text-xs text-gray-500">All-time usage hours</p>
            </div>
        </div>

        <!-- Usage Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Screen Type Usage -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Usage by Screen Type</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Queue Screens</span>
                            <span class="text-lg font-bold text-blue-600">{{ number_format($metrics['usage_by_type']['queue'], 0) }} hrs</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $totalUsage = $metrics['usage_by_type']['queue'] + ($metrics['usage_by_type']['service'] ?? 0);
                                $queuePercent = $totalUsage > 0 ? ($metrics['usage_by_type']['queue'] / $totalUsage) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $queuePercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Service Screens</span>
                            <span class="text-lg font-bold text-teal-600">{{ number_format($metrics['usage_by_type']['service'] ?? 0, 0) }} hrs</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $servicePercent = $totalUsage > 0 ? (($metrics['usage_by_type']['service'] ?? 0) / $totalUsage) * 100 : 0;
                            @endphp
                            <div class="bg-teal-600 h-3 rounded-full" style="width: {{ $servicePercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Breakdown -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Subscriptions by Plan</h3>
                @if(count($metrics['subscription_breakdown']) > 0)
                    <div class="space-y-4">
                        @foreach($metrics['subscription_breakdown'] as $planName => $count)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm font-medium text-gray-700">{{ $planName }}</span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-bold">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No active subscriptions</p>
                @endif
            </div>
        </div>

        <!-- Tenant Status Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Tenants by Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="text-center p-6 bg-green-50 rounded-lg border border-green-200">
                    <div class="text-4xl font-bold text-green-600 mb-2">{{ number_format($metrics['tenants_by_status']['active']) }}</div>
                    <div class="text-sm font-medium text-green-700">Active Tenants</div>
                </div>
                <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-4xl font-bold text-gray-600 mb-2">{{ number_format($metrics['tenants_by_status']['inactive']) }}</div>
                    <div class="text-sm font-medium text-gray-700">Inactive Tenants</div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
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
                            <li>Active tenants are defined as those with activity in the last 30 days</li>
                            <li>Screen usage hours include only completed sessions</li>
                            <li>This dashboard is read-only and does not expose personal data</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

