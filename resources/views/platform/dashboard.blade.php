@extends('layouts.platform')

@section('title', 'Platform Dashboard - SmartQueue')

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
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .status-trial {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-expired {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    .status-no-subscription {
        background-color: #e0e7ff;
        color: #3730a3;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 py-3">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <h1 class="text-lg sm:text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Platform Dashboard</h1>
                    </div>
                    <span class="hidden lg:block text-xs sm:text-sm text-gray-500 truncate">Super Admin Control Panel</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                    <span class="hidden sm:block text-xs sm:text-sm text-gray-600 truncate max-w-[100px] sm:max-w-none">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 inline-block sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden xs:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6 sm:mb-8">
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200">
                <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Total Tenants</div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200">
                <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Active</div>
                <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $stats['active_tenants'] }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200">
                <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Inactive</div>
                <div class="text-2xl sm:text-3xl font-bold text-red-600">{{ $stats['inactive_tenants'] }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200">
                <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">With Subscription</div>
                <div class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $stats['tenants_with_subscription'] }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-200">
                <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">On Trial</div>
                <div class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $stats['tenants_on_trial'] }}</div>
            </div>
        </div>

        <!-- Tenants Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">All Tenants</h2>
            </div>
            
            @if($tenants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Subscription</th>
                                <th class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tenants as $tenant)
                                @php
                                    $subscription = $tenant->subscription;
                                    $plan = $subscription ? $subscription->plan : null;
                                    $isOnTrial = $tenant->isOnTrial();
                                    $hasActiveSubscription = $subscription && $subscription->isActive();
                                    
                                    // Determine status
                                    if (!$tenant->is_active) {
                                        $status = 'inactive';
                                        $statusLabel = 'Inactive';
                                    } elseif ($isOnTrial) {
                                        $status = 'trial';
                                        $statusLabel = 'Trial';
                                    } elseif ($hasActiveSubscription) {
                                        $status = 'active';
                                        $statusLabel = 'Active';
                                    } elseif ($subscription && !$subscription->isActive()) {
                                        $status = 'expired';
                                        $statusLabel = 'Expired';
                                    } else {
                                        $status = 'no-subscription';
                                        $statusLabel = 'No Subscription';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                        <div class="text-xs text-gray-500 sm:hidden">{{ $tenant->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="text-sm text-gray-500">{{ $tenant->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-{{ $status }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        @if($plan)
                                            <div class="text-sm font-medium text-gray-900">{{ $plan->name }}</div>
                                            @if($isOnTrial && $tenant->trial_ends_at)
                                                <div class="text-xs text-gray-500">Trial ends {{ $tenant->trial_ends_at->diffForHumans() }}</div>
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-400">—</div>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        @if($subscription)
                                            @if($subscription->isActive())
                                                <div class="text-sm text-gray-900">
                                                    @if($subscription->ends_at)
                                                        Expires {{ $subscription->ends_at->format('M d, Y') }}
                                                    @else
                                                        Active
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">Expired</div>
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-400">—</div>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form method="POST" action="{{ route('tenant.switch', $tenant->slug) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900 font-semibold transition-colors">
                                                Enter
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 sm:px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tenants</h3>
                    <p class="mt-1 text-sm text-gray-500">No tenants have been created yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

