@extends('layouts.tenant')

@section('title', 'Subscription & Billing - SmartQueue')

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
                <div class="flex gap-2">
                    <a href="{{ route('app.invoices.index') }}" class="px-3 sm:px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        View Invoices
                    </a>
                    <a href="{{ route('app.dashboard') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        Back to Dashboard
                    </a>
                </div>
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
                                    @if($subscription->payment_method)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Payment: {{ ucfirst($subscription->payment_method) }}
                                        </p>
                                    @endif
                                @elseif($isExpired)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending Payment
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Pending Payment
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Expiry Date & Payment Actions -->
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

                            <!-- Payment Actions (show when subscription is not active) -->
                            @if(!$subscription->isActive() && !$isOnTrial)
                                <div class="pt-2">
                                    <button onclick="showPaymentModal()" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm touch-manipulation mb-2">
                                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        View Payment Instructions
                                    </button>
                                    <button onclick="notifyPayment()" class="w-full px-4 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm touch-manipulation">
                                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        Notify Payment
                                    </button>
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
                                $isRecommended = isset($recommendedPlanSlug) && $plan->slug === $recommendedPlanSlug && !$isCurrentPlan;
                            @endphp
                            <div class="relative bg-white rounded-xl border-2 {{ $isCurrentPlan ? 'border-blue-500 shadow-xl ring-4 ring-blue-100' : ($isRecommended ? 'border-purple-500 shadow-xl ring-4 ring-purple-100' : 'border-gray-200') }} p-4 sm:p-6 hover:shadow-lg transition-all duration-300">
                                @if($isCurrentPlan)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-600 text-white">
                                            Current Plan
                                        </span>
                                    </div>
                                @elseif($isRecommended)
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                                            ⭐ Recommended
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
                                            @if($plan->max_clinics == -1)
                                                <span class="text-purple-600">Unlimited</span>
                                            @else
                                                {{ $plan->max_clinics }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Staff Members</span>
                                        <span class="font-semibold text-gray-900">
                                            @if($plan->max_users == -1)
                                                <span class="text-purple-600">Unlimited</span>
                                            @else
                                                {{ $plan->max_users }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Display Screens</span>
                                        <span class="font-semibold text-gray-900">
                                            @if($plan->max_screens == -1)
                                                <span class="text-purple-600">Unlimited</span>
                                            @else
                                                {{ $plan->max_screens }}
                                            @endif
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
                                    <div class="space-y-2">
                                        @if($stripeEnabled && $plan->stripe_price_id && $plan->price > 0)
                                            <form action="{{ route('app.stripe.checkout', $plan) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm touch-manipulation">
                                                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                    </svg>
                                                    Pay with Card (Stripe)
                                                </button>
                                            </form>
                                        @endif
                                        <button onclick="showUpgradeMessage('{{ $plan->name }}')" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm touch-manipulation">
                                            {{ $plan->price > 0 ? ($stripeEnabled && $plan->stripe_price_id ? 'Manual Payment' : 'Upgrade to ' . $plan->name) : 'Select Plan' }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Instructions Section (shown when subscription is not active) -->
        @if($subscription && !$subscription->isActive() && !$isOnTrial)
        <div class="mb-6 sm:mb-8 fade-in">
            <div class="bg-amber-50 border-2 border-amber-200 rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8">
                <div class="flex items-start gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-lg sm:text-xl font-bold text-amber-900 mb-2">Payment Required</h3>
                        <p class="text-sm sm:text-base text-amber-800 mb-4">
                            Your subscription requires payment to be activated. Please complete the payment using the instructions below and notify us once payment is made.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <button onclick="showPaymentModal()" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm sm:text-base touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                View Payment Instructions
                            </button>
                            <button onclick="notifyPayment()" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm sm:text-base touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Notify Payment Made
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

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

<!-- Payment Instructions Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-2xl w-full mx-auto transform transition-all my-8">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Payment Instructions</h2>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 sm:p-6">
            <!-- Plan Information -->
            @if($currentPlan)
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Subscription Plan</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $currentPlan->name }}</p>
                @if($currentPlan->price > 0)
                    <p class="text-base sm:text-lg text-gray-700 mt-1">
                        <strong>${{ number_format($currentPlan->price, 2) }}</strong>
                        <span class="text-sm text-gray-500">/ {{ ucfirst($currentPlan->billing_cycle ?? 'month') }}</span>
                    </p>
                @endif
            </div>
            @endif

            <!-- Bank Details -->
            <div class="mb-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Bank Transfer Details</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sm:p-6 space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">Account Name:</span>
                        <span class="text-sm text-gray-900 font-mono text-right">SmartQueue Inc.</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">Account Number:</span>
                        <span class="text-sm text-gray-900 font-mono text-right">1234567890</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">Bank Name:</span>
                        <span class="text-sm text-gray-900 text-right">Example Bank</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">Branch:</span>
                        <span class="text-sm text-gray-900 text-right">Main Branch</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">IFSC Code:</span>
                        <span class="text-sm text-gray-900 font-mono text-right">EXMP0001234</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-semibold text-gray-700">SWIFT Code:</span>
                        <span class="text-sm text-gray-900 font-mono text-right">EXMPUS33</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Placeholder -->
            <div class="mb-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">QR Code Payment</h3>
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 sm:p-12 flex flex-col items-center justify-center">
                    <div class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 text-center">QR Code placeholder - Contact support for actual QR code</p>
                </div>
            </div>

            <!-- Payment Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-900 mb-1">Important Notes:</p>
                        <ul class="text-xs sm:text-sm text-amber-800 space-y-1 list-disc list-inside">
                            <li>Please include your organization name in the payment reference</li>
                            <li>Payment processing may take 1-2 business days</li>
                            <li>Your subscription will be activated once payment is confirmed</li>
                            <li>Contact support if you have any questions</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Support Contact -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm font-semibold text-blue-900 mb-2">Need Help?</p>
                <p class="text-xs sm:text-sm text-blue-800 mb-2">
                    If you have questions about payment or need assistance, please contact our support team:
                </p>
                <div class="space-y-1 text-xs sm:text-sm text-blue-800">
                    <p><strong>Email:</strong> <a href="mailto:support@example.com" class="underline font-semibold hover:text-blue-900">support@example.com</a></p>
                    <p><strong>Phone:</strong> <a href="tel:+1234567890" class="underline font-semibold hover:text-blue-900">+1 (234) 567-8900</a></p>
                    <p><strong>Hours:</strong> Monday - Friday, 9 AM - 6 PM</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="closePaymentModal()" class="flex-1 px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-sm">
                    Close
                </button>
                <button onclick="notifyPayment(); closePaymentModal();" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-sm">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notify Payment Made
                </button>
            </div>
        </div>
    </div>
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
    function showPaymentModal() {
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function notifyPayment() {
        const orgName = '{{ $tenant->name ?? "My Organization" }}';
        const planName = '{{ $currentPlan->name ?? "Subscription" }}';
        const planPrice = '{{ $currentPlan->price ?? 0 }}';
        const subject = encodeURIComponent(`Payment Notification - ${orgName}`);
        const body = encodeURIComponent(`Hello,\n\nI have made the payment for my subscription.\n\nOrganization: ${orgName}\nPlan: ${planName}\nAmount: $${planPrice}\n\nPlease activate my subscription.\n\nThank you!`);
        window.location.href = `mailto:support@example.com?subject=${subject}&body=${body}`;
    }

    function showUpgradeMessage(planName) {
        document.getElementById('planName').textContent = planName;
        const modal = document.getElementById('upgradeModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
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
        document.body.style.overflow = '';
    }

    // Close modals on outside click
    document.getElementById('paymentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });

    document.getElementById('upgradeModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeUpgradeModal();
        }
    });

    // Close modals on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentModal();
            closeUpgradeModal();
        }
    });
</script>
@endpush

<!-- Footer Links -->
<footer class="bg-gray-50 border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-600">
                &copy; {{ date('Y') }} SmartQueue. All rights reserved.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
                <a href="{{ route('legal.terms') }}" class="text-gray-600 hover:text-blue-600 transition-colors" target="_blank">Terms of Service</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('legal.privacy') }}" class="text-gray-600 hover:text-blue-600 transition-colors" target="_blank">Privacy Policy</a>
                <span class="text-gray-300">|</span>
                <a href="{{ route('legal.refunds') }}" class="text-gray-600 hover:text-blue-600 transition-colors" target="_blank">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>
@endsection
