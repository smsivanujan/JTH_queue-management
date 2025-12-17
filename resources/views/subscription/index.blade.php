@extends('layouts.tenant')

@section('title', 'Subscription & Billing - SmartQueue Hospital')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
        animation: fadeIn 0.3s ease-out;
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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Subscription & Billing</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Manage your plan and view usage</p>
                </div>
                <a href="{{ route('app.dashboard') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Current Plan Overview -->
        <div class="mb-6 sm:mb-8 fade-in">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 lg:p-8">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Current Plan</h2>
                
                @if($subscription && $currentPlan)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Plan Name & Status -->
                        <div class="space-y-3 sm:space-y-4">
                            <div>
                                <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Plan Name</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $currentPlan->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Status</p>
                                @if($isOnTrial)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Trial Period
                                    </span>
                                    @if($trialDaysRemaining !== null)
                                        <p class="text-xs sm:text-sm text-gray-600 mt-2">
                                            <strong>{{ $trialDaysRemaining }}</strong> {{ $trialDaysRemaining == 1 ? 'day' : 'days' }} remaining
                                        </p>
                                    @endif
                                @elseif($subscription->isActive())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Active
                                    </span>
                                @elseif($isExpired)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Expired
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Expiry Date -->
                        <div class="space-y-3 sm:space-y-4">
                            @if($subscription->ends_at)
                                <div>
                                    <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Expiry Date</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ $subscription->ends_at->format('F d, Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $subscription->ends_at->isFuture() ? 'Expires in ' . $subscription->ends_at->diffForHumans() : 'Expired ' . $subscription->ends_at->diffForHumans() }}
                                    </p>
                                </div>
                            @else
                                <div>
                                    <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Expiry Date</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">No expiry</p>
                                </div>
                            @endif
                            
                            @if($currentPlan->price > 0)
                                <div>
                                    <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Price</p>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        ${{ number_format($currentPlan->price, 2) }}
                                        <span class="text-xs sm:text-sm font-normal text-gray-500">/ {{ ucfirst($currentPlan->billing_cycle ?? 'month') }}</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 sm:p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm sm:text-base font-semibold text-amber-900 mb-1">No Active Subscription</h3>
                                <p class="text-xs sm:text-sm text-amber-800 mb-3">You don't have an active subscription. Please select a plan to continue using the service.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Usage vs Limits -->
        @if($subscription && $currentPlan)
        <div class="mb-6 sm:mb-8 fade-in">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 lg:p-8">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Usage & Limits</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Clinics Usage -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-4 sm:p-6 border border-blue-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Clinics</h3>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">
                            {{ $clinicsUsed }}
                            @if($maxClinics !== null)
                                <span class="text-base sm:text-lg font-normal text-gray-500">/ {{ $maxClinics == -1 ? '∞' : $maxClinics }}</span>
                            @endif
                        </div>
                        @if($maxClinics !== null && $maxClinics != -1)
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($clinicsUsed / $maxClinics) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Staff Usage -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 sm:p-6 border border-purple-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Staff Members</h3>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">
                            {{ $staffUsed }}
                            @if($maxUsers !== null)
                                <span class="text-base sm:text-lg font-normal text-gray-500">/ {{ $maxUsers == -1 ? '∞' : $maxUsers }}</span>
                            @endif
                        </div>
                        @if($maxUsers !== null && $maxUsers != -1)
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($staffUsed / $maxUsers) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Screens Usage -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 sm:p-6 border border-green-200">
                        <div class="flex items-center justify-between mb-2 sm:mb-3">
                            <h3 class="text-xs sm:text-sm font-semibold text-gray-700">Active Screens</h3>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">
                            {{ $activeScreensCount }}
                            @if($maxScreens !== null)
                                <span class="text-base sm:text-lg font-normal text-gray-500">/ {{ $maxScreens == -1 ? '∞' : $maxScreens }}</span>
                            @endif
                        </div>
                        @if($maxScreens !== null && $maxScreens != -1)
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($activeScreensCount / $maxScreens) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Available Plans -->
        <div class="mb-6 sm:mb-8 fade-in">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Available Plans</h2>
                    <p class="text-xs sm:text-sm text-gray-600">Choose a plan that fits your needs</p>
                </div>

                @if($availablePlans->isEmpty())
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 sm:p-8 text-center">
                        <p class="text-sm sm:text-base text-gray-600">No plans available at this time.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($availablePlans as $plan)
                            @php
                                $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                            @endphp
                            <div class="relative bg-white rounded-xl border-2 {{ $isCurrentPlan ? 'border-blue-500 shadow-xl ring-4 ring-blue-100' : 'border-gray-200' }} p-4 sm:p-6 hover:shadow-lg transition-all duration-300">
                                @if($isCurrentPlan)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white">
                                            Current Plan
                                        </span>
                                    </div>
                                @endif

                                <!-- Plan Header -->
                                <div class="mb-4 sm:mb-6">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                    @if($plan->description)
                                        <p class="text-xs sm:text-sm text-gray-600 mb-3">{{ $plan->description }}</p>
                                    @endif
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl sm:text-4xl font-bold text-gray-900">
                                            ${{ number_format($plan->price, 2) }}
                                        </span>
                                        @if($plan->price > 0)
                                            <span class="text-sm text-gray-500">/ {{ ucfirst($plan->billing_cycle ?? 'month') }}</span>
                                        @else
                                            <span class="text-sm text-gray-500">Free</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Plan Limits -->
                                <div class="space-y-3 mb-4 sm:mb-6">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Clinics</span>
                                        <span class="font-semibold text-gray-900">
                                            {{ $plan->max_clinics == -1 ? 'Unlimited' : $plan->max_clinics }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Staff Members</span>
                                        <span class="font-semibold text-gray-900">
                                            {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Display Screens</span>
                                        <span class="font-semibold text-gray-900">
                                            {{ $plan->max_screens == -1 ? 'Unlimited' : $plan->max_screens }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Features -->
                                @if(!empty($plan->features))
                                    <div class="border-t border-gray-200 pt-4 mb-4 sm:mb-6">
                                        <p class="text-xs font-semibold text-gray-700 mb-3">Features:</p>
                                        <ul class="space-y-2">
                                            @foreach($plan->features as $feature)
                                                <li class="flex items-start text-xs sm:text-sm text-gray-600">
                                                    <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Upgrade Button -->
                                @if($isCurrentPlan)
                                    <button disabled class="w-full px-4 py-2.5 bg-gray-300 text-gray-600 rounded-lg font-semibold cursor-not-allowed text-sm">
                                        Current Plan
                                    </button>
                                @else
                                    <button onclick="showUpgradeMessage('{{ $plan->name }}')" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm touch-manipulation">
                                        {{ $plan->price > 0 ? 'Upgrade to ' . $plan->name : 'Select Plan' }}
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Upgrade Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 fade-in">
            <div class="flex items-start gap-3 sm:gap-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm sm:text-base font-semibold text-blue-900 mb-2">Manual Plan Upgrades</h3>
                    <p class="text-xs sm:text-sm text-blue-800 mb-3">
                        Plan upgrades are processed manually. To upgrade your plan, please contact our support team with your organization name and the plan you'd like to upgrade to.
                    </p>
                    <p class="text-xs sm:text-sm text-blue-700">
                        <strong>Contact Support:</strong> <a href="mailto:support@example.com" class="underline font-semibold hover:text-blue-900">support@example.com</a>
                    </p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Upgrade Message Modal -->
<div id="upgradeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-md w-full mx-auto transform transition-all">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Upgrade Plan</h2>
        </div>
        <div class="p-4 sm:p-6">
            <p class="text-sm sm:text-base text-gray-700 mb-4">
                To upgrade to <strong id="planName"></strong>, please contact our support team.
            </p>
            <p class="text-xs sm:text-sm text-gray-600 mb-6">
                We'll process your upgrade request manually and activate your new plan as soon as possible.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4 mb-6">
                <p class="text-xs sm:text-sm font-semibold text-blue-900 mb-1">Contact Information</p>
                <p class="text-xs sm:text-sm text-blue-800">
                    Email: <a href="mailto:support@example.com" class="underline font-semibold">support@example.com</a>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="closeUpgradeModal()" class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-sm">
                    Close
                </button>
                <a href="mailto:support@example.com?subject=Plan Upgrade Request&body=Hello, I would like to upgrade to the [PLAN NAME] plan. My organization is: [ORGANIZATION NAME]" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-center text-sm">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showUpgradeMessage(planName) {
        document.getElementById('planName').textContent = planName;
        const modal = document.getElementById('upgradeModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Update email link with plan name
        const emailLink = modal.querySelector('a[href^="mailto:"]');
        if (emailLink) {
            const orgName = '{{ $tenant->name ?? "My Organization" }}';
            const subject = encodeURIComponent(`Plan Upgrade Request - ${planName}`);
            const body = encodeURIComponent(`Hello,\n\nI would like to upgrade to the ${planName} plan.\n\nMy organization is: ${orgName}\n\nThank you!`);
            emailLink.href = `mailto:support@example.com?subject=${subject}&body=${body}`;
        }
    }

    function closeUpgradeModal() {
        const modal = document.getElementById('upgradeModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal on outside click
    document.getElementById('upgradeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeUpgradeModal();
        }
    });
</script>
@endpush
@endsection
