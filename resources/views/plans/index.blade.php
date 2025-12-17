@extends('layouts.tenant')

@section('title', 'Available Plans - SmartQueue Hospital')

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
        animation: fadeIn 0.4s ease-out;
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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Available Plans</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Choose a plan that fits your needs</p>
                </div>
                <a href="{{ route('app.subscription.index') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @if(session('success'))
            <div class="mb-4 sm:mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm sm:text-base">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <div class="font-semibold mb-1 text-sm sm:text-base">Please fix the following errors:</div>
                        <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Current Plan Notice -->
        @if($currentSubscription && $currentPlan)
            <div class="mb-4 sm:mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 fade-in">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-1">Current Plan: {{ $currentPlan->name }}</h3>
                        <p class="text-xs sm:text-sm text-blue-800">You are currently on the <strong>{{ $currentPlan->name }}</strong> plan. Select a plan below to upgrade or change your subscription.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Manual Payment Notice -->
        <div class="mb-4 sm:mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 sm:p-6 fade-in">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base sm:text-lg font-semibold text-amber-900 mb-1">Manual Activation Process</h3>
                    <p class="text-xs sm:text-sm text-amber-800 mb-2">After selecting a plan, your subscription will be activated manually by our team. You will receive confirmation once activation is complete.</p>
                    <p class="text-xs sm:text-sm text-amber-700 font-medium">💡 Tip: Contact support via WhatsApp for faster activation or if you have questions about plans.</p>
                </div>
            </div>
        </div>

        <!-- Plans Grid -->
        @if($plans->isEmpty())
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-8 sm:p-12 text-center fade-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">No Plans Available</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto">There are currently no subscription plans available. Please contact support for assistance.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                @foreach($plans as $plan)
                    @php
                        $isCurrentPlan = $currentPlan && $currentPlan->id === $plan->id;
                        $maxClinics = $plan->max_clinics == -1 ? 'Unlimited' : $plan->max_clinics;
                        $maxUsers = $plan->max_users == -1 ? 'Unlimited' : $plan->max_users;
                        $maxScreens = ($plan->max_screens ?? 1) == -1 ? 'Unlimited' : ($plan->max_screens ?? 1);
                    @endphp
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 {{ $isCurrentPlan ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }} p-6 sm:p-8 fade-in {{ $isCurrentPlan ? 'transform scale-105' : '' }}">
                        @if($isCurrentPlan)
                            <div class="mb-4 -mt-2 -mx-2 sm:-mx-4">
                                <div class="bg-blue-600 text-white text-xs sm:text-sm font-semibold py-1.5 px-4 rounded-t-xl text-center">
                                    Current Plan
                                </div>
                            </div>
                        @endif

                        <div class="text-center mb-6">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                            @if($plan->description)
                                <p class="text-xs sm:text-sm text-gray-600 mb-4">{{ $plan->description }}</p>
                            @endif
                            <div class="flex items-baseline justify-center mb-2">
                                @if($plan->price > 0)
                                    <span class="text-3xl sm:text-4xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                                    <span class="text-gray-600 ml-2 text-sm sm:text-base">/{{ $plan->billing_cycle }}</span>
                                @else
                                    <span class="text-3xl sm:text-4xl font-bold text-green-600">Free</span>
                                @endif
                            </div>
                            @if($plan->trial_days > 0)
                                <p class="text-xs text-green-600 font-semibold">{{ $plan->trial_days }}-day free trial</p>
                            @endif
                        </div>

                        <!-- Limits -->
                        <div class="mb-6 space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-xs sm:text-sm text-gray-600">Max Clinics</span>
                                <span class="text-sm sm:text-base font-semibold text-gray-900">{{ $maxClinics }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-xs sm:text-sm text-gray-600">Max Users</span>
                                <span class="text-sm sm:text-base font-semibold text-gray-900">{{ $maxUsers }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-xs sm:text-sm text-gray-600">Max Screens</span>
                                <span class="text-sm sm:text-base font-semibold text-gray-900">{{ $maxScreens }}</span>
                            </div>
                        </div>

                        <!-- Features -->
                        @if($plan->features && count($plan->features) > 0)
                            <div class="mb-6">
                                <h4 class="text-xs sm:text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Features</h4>
                                <ul class="space-y-2">
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-start gap-2 text-xs sm:text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Action Button -->
                        @if($isCurrentPlan)
                            <button disabled class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-semibold cursor-not-allowed text-xs sm:text-sm">
                                Current Plan
                            </button>
                        @else
                            <form action="{{ route('app.plans.activate', ['plan' => $plan->slug]) }}" method="POST" onsubmit="return confirm('Are you sure you want to activate the {{ $plan->name }} plan? This will replace your current subscription.');">
                                @csrf
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                                    @if($currentSubscription)
                                        Switch to {{ $plan->name }}
                                    @else
                                        Activate {{ $plan->name }}
                                    @endif
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 text-center mt-2">Manual activation required</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Additional Information -->
        <div class="mt-8 sm:mt-12 grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <!-- Support Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md border border-gray-200 p-6 fade-in">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Need Help Choosing?</h3>
                </div>
                <p class="text-xs sm:text-sm text-gray-600 mb-4">Contact our support team via WhatsApp for personalized assistance in selecting the right plan for your organization.</p>
                <p class="text-xs sm:text-sm text-gray-500">Response time: Usually within 1 hour</p>
            </div>

            <!-- Activation Process Card -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md border border-gray-200 p-6 fade-in">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Activation Process</h3>
                </div>
                <ol class="space-y-2 text-xs sm:text-sm text-gray-600 list-decimal list-inside">
                    <li>Select your desired plan</li>
                    <li>Submit the activation request</li>
                    <li>Our team will review and activate</li>
                    <li>You'll receive confirmation via email</li>
                </ol>
            </div>
        </div>
    </main>
</div>
@endsection

