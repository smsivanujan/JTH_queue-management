@extends('layouts.tenant')

@section('title', 'Dashboard - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Hide layout navigation and footer for dashboard custom design */
    nav.navbar {
        display: none;
    }
    footer {
        display: none;
    }
    
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
    .clinic-card {
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    .clinic-card:nth-child(1) { animation-delay: 0.1s; }
    .clinic-card:nth-child(2) { animation-delay: 0.2s; }
    .clinic-card:nth-child(3) { animation-delay: 0.3s; }
    .clinic-card:nth-child(4) { animation-delay: 0.4s; }
    .clinic-card:nth-child(5) { animation-delay: 0.5s; }
    .clinic-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 py-3 border-b border-gray-200">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h1 class="text-lg sm:text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">SmartQueue</h1>
                    </div>
                    <span class="hidden lg:block text-xs sm:text-sm text-gray-500 truncate">Queue Management System</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto justify-between sm:justify-end">
                    @if(isset($tenant))
                        <div class="hidden lg:flex flex-col items-end">
                            <span class="text-xs text-gray-500">Organization</span>
                            <span class="text-sm font-semibold text-gray-700 truncate max-w-[150px]">{{ $tenant->name }}</span>
                        </div>
                    @endif
                    @if(auth()->check() && auth()->user()->isSuperAdmin() && isset($tenant))
                        <form action="{{ route('tenant.exit') }}" method="POST" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="px-3 sm:px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 inline-block sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                <span class="hidden xs:inline">Exit Tenant</span>
                            </button>
                        </form>
                    @endif
                    <div class="flex items-center gap-2">
                        <span class="hidden sm:block text-xs sm:text-sm text-gray-600 truncate max-w-[100px] sm:max-w-none">{{ Auth::user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 inline-block sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="hidden xs:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Navigation Bar -->
            <nav class="flex flex-wrap items-center gap-3 sm:gap-6 py-3">
                <a href="{{ route('app.dashboard') }}" class="text-xs sm:text-sm {{ request()->routeIs('app.dashboard') ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-gray-600 hover:text-blue-600' }} transition-colors pb-2 sm:pb-3 px-1">
                    Dashboard
                </a>
                @admin
                <a href="{{ route('app.staff.index') }}" class="text-xs sm:text-sm {{ request()->routeIs('app.staff.*') ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-gray-600 hover:text-blue-600' }} transition-colors pb-2 sm:pb-3 px-1">
                    Staff
                </a>
                <a href="{{ route('app.clinic.index') }}" class="text-xs sm:text-sm {{ request()->routeIs('app.clinic.*') ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-gray-600 hover:text-blue-600' }} transition-colors pb-2 sm:pb-3 px-1">
                    Locations
                </a>
                <a href="{{ route('app.subscription.index') }}" class="hidden sm:inline text-xs sm:text-sm {{ request()->routeIs('app.subscription.*') || request()->routeIs('app.plans.*') ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-gray-600 hover:text-blue-600' }} transition-colors pb-2 sm:pb-3 px-1">
                    Billing & Subscription
                </a>
                <a href="{{ route('app.subscription.index') }}" class="sm:hidden text-xs sm:text-sm {{ request()->routeIs('app.subscription.*') || request()->routeIs('app.plans.*') ? 'font-semibold text-blue-600 border-b-2 border-blue-600' : 'font-medium text-gray-600 hover:text-blue-600' }} transition-colors pb-2 sm:pb-3 px-1">
                    Billing
                </a>
                @endadmin
                @if(!auth()->user()->isAdmin())
                <!-- Non-admin users see restricted navigation items with explanation -->
                <div class="flex items-center gap-2 sm:gap-3 relative group">
                    <span class="text-xs sm:text-sm font-medium text-gray-400 cursor-help">Staff</span>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="absolute top-full left-0 mt-2 hidden group-hover:block w-56 z-50">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
                            <div class="font-semibold mb-1">Admin Only</div>
                            <div>Staff management requires Administrator role. Contact your admin for access.</div>
                            <div class="absolute top-0 left-4 -mt-1">
                                <div class="border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 relative group hidden sm:flex">
                    <span class="text-xs sm:text-sm font-medium text-gray-400 cursor-help">Billing & Subscription</span>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="absolute top-full left-0 mt-2 hidden group-hover:block w-56 z-50">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
                            <div class="font-semibold mb-1">Admin Only</div>
                            <div>Subscription management requires Administrator role. Contact your admin for access.</div>
                            <div class="absolute top-0 left-4 -mt-1">
                                <div class="border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 relative group hidden md:flex">
                    <span class="text-xs sm:text-sm font-medium text-gray-400 cursor-help">Enterprise Metrics</span>
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="absolute top-full left-0 mt-2 hidden group-hover:block w-56 z-50">
                        <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
                            <div class="font-semibold mb-1">Admin Only</div>
                            <div>Enterprise metrics requires Administrator role. Contact your admin for access.</div>
                            <div class="absolute top-0 left-4 -mt-1">
                                <div class="border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endadmin
                <div class="ml-auto flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="hidden sm:inline">{{ now()->format('l, F j, Y') }}</span>
                    <span class="sm:hidden">{{ now()->format('M j, Y') }}</span>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Section (Optional) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Total Locations</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $clinics->count() }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600">Active Queues</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $clinics->count() }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations Grid -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Select Location</h2>
                @admin
                    @php
                        $canCreateMoreClinic = \App\Helpers\SubscriptionHelper::canCreateClinic();
                        $currentClinicCount = $clinics->count();
                        $plan = \App\Helpers\SubscriptionHelper::getCurrentPlan();
                        $tenant = app()->bound('tenant') ? app('tenant') : null;
                        $maxClinics = null;
                        if ($plan && $tenant) {
                            $subscription = $tenant->subscription;
                            $maxClinics = $subscription ? ($subscription->max_clinics ?? $plan->max_clinics) : $plan->max_clinics;
                        }
                    @endphp
                    <div class="flex items-center gap-2 sm:gap-3">
                        @if($canCreateMoreClinic)
                            <a href="{{ route('app.clinic.create') }}" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="hidden xs:inline">Add Location</span>
                            </a>
                        @else
                            <div class="relative group">
                                <button disabled class="px-3 sm:px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed text-xs sm:text-sm opacity-60">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="hidden xs:inline">Add Location</span>
                                </button>
                                <div class="absolute bottom-full mb-2 right-0 hidden group-hover:block w-64 z-50">
                                    <div class="bg-gray-900 text-white text-xs sm:text-sm rounded-lg py-2 px-3 shadow-lg">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <div class="font-semibold mb-1">Location Limit Reached</div>
                                                <div>You have {{ $currentClinicCount }} of {{ $maxClinics == -1 ? 'unlimited' : $maxClinics }} locations. <a href="{{ route('app.plans.index') }}" class="underline font-semibold">Upgrade plan</a> to add more.</div>
                                            </div>
                                        </div>
                                        <div class="absolute top-full right-4 -mt-1">
                                            <div class="border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('app.clinic.index') }}" class="px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all text-xs sm:text-sm touch-manipulation">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span class="hidden sm:inline">Manage</span>
                        </a>
                    </div>
                @endadmin
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <!-- Location Queue Cards -->
                @foreach ($clinics as $clinic)
                <a href="{{ route('app.queues.index', $clinic) }}" class="clinic-card">
                    <div class="w-full bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-blue-200 p-4 sm:p-6 hover:shadow-xl hover:border-blue-300 transition-all duration-300 transform hover:-translate-y-1 text-left touch-manipulation">
                        <div class="flex flex-col">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 shadow-lg mx-auto">
                                <img src="{{ asset('public/images/clinics/' . $clinic->id . '.ico') }}" alt="{{ $clinic->name }}" class="w-10 h-10 sm:w-12 sm:h-12 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center" style="display:none;">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1 sm:mb-2 text-center">{{ $clinic->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mb-2 text-center">Queue Management</p>
                            <div class="w-full bg-blue-50 rounded-lg px-2 sm:px-3 py-1 sm:py-1.5 border border-blue-200 mb-1">
                                <span class="text-xs font-semibold text-blue-700 text-center block">Queue System</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Empty State (if no locations) -->
        @if($clinics->isEmpty())
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-8 sm:p-12 text-center fade-in">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">No Locations Available</h3>
            @admin
                @php
                    $canCreateMoreClinic = \App\Helpers\SubscriptionHelper::canCreateClinic();
                @endphp
                @if($canCreateMoreClinic)
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto">Get started by adding your first location to manage queues.</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                        <a href="{{ route('app.clinic.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all touch-manipulation text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Your First Location
                        </a>
                    </div>
                @else
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto">Please contact your administrator to add locations to your organization.</p>
                @endif
            @endadmin
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">SmartQueue</h3>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Modern queue management system. Streamline service flow, reduce wait times, and improve efficiency.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-semibold text-gray-900 mb-3 sm:mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-xs sm:text-sm text-gray-600">
                        <li><a href="{{ route('app.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a></li>
                        @admin
                        <li><a href="{{ route('app.staff.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Staff Management
                        </a></li>
                        <li><a href="{{ route('app.subscription.index') }}" class="hover:text-blue-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Billing & Subscription
                        </a></li>
                        @endadmin
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-semibold text-gray-900 mb-3 sm:mb-4">Support</h4>
                    <ul class="space-y-2 sm:space-y-3 text-xs sm:text-sm text-gray-600">
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>WhatsApp Support Available</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>24/7 System Access</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Secure & Reliable</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <p class="text-xs sm:text-sm text-gray-500 text-center sm:text-left">
                        &copy; {{ date('Y') }} SmartQueue. All rights reserved. | Universal Queue Management System
                    </p>
                    <div class="flex items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                        <span>Version 1.0</span>
                        <span class="hidden sm:inline">•</span>
                        <span>Powered by Laravel</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

@endsection
