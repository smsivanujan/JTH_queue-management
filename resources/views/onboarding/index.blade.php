@extends('layouts.tenant')

@section('title', 'Welcome - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sm:p-8 lg:p-12 fade-in">
            <!-- Welcome Header -->
            <div class="text-center mb-8 sm:mb-12">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Welcome to SmartQueue!</h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    Let's get your queue management system set up in just a few simple steps.
                </p>
            </div>

            <!-- Steps Overview -->
            <div class="mb-8 sm:mb-12">
                <div class="space-y-6">
                    <!-- Step 1: Clinic -->
                    @if(!$hasClinics)
                        <div class="flex items-start gap-4 p-4 sm:p-6 bg-blue-50 rounded-xl border-2 border-blue-200">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl">
                                1
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Create Your First Location</h3>
                                <p class="text-sm sm:text-base text-gray-600 mb-4">
                                    Add your first location or clinic where you'll manage queues.
                                </p>
                                <a href="{{ route('app.onboarding.clinic') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-sm sm:text-base touch-manipulation">
                                    Get Started
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4 p-4 sm:p-6 bg-green-50 rounded-xl border-2 border-green-200">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-600 text-white rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Location Created ✓</h3>
                                <p class="text-sm sm:text-base text-gray-600">
                                    You've already created your first location. Great job!
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Service -->
                    @if(!$hasServices)
                        <div class="flex items-start gap-4 p-4 sm:p-6 bg-indigo-50 rounded-xl border-2 border-indigo-200 {{ !$hasClinics ? 'opacity-60' : '' }}">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-lg sm:text-xl {{ !$hasClinics ? 'opacity-50' : '' }}">
                                2
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Create Your First Service</h3>
                                <p class="text-sm sm:text-base text-gray-600 mb-4">
                                    Set up your first service queue (e.g., Customer Service, Order Pickup, Consultation).
                                </p>
                                @if($hasClinics)
                                    <a href="{{ route('app.onboarding.service') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-sm sm:text-base touch-manipulation">
                                        Continue
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <p class="text-xs sm:text-sm text-gray-500 italic">Complete step 1 first</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-4 p-4 sm:p-6 bg-green-50 rounded-xl border-2 border-green-200">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-600 text-white rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Service Created ✓</h3>
                                <p class="text-sm sm:text-base text-gray-600">
                                    You've already created your first service. Excellent!
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-6 sm:pt-8 border-t border-gray-200">
                <form action="{{ route('app.onboarding.skip') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-6 sm:px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-sm sm:text-base touch-manipulation">
                        Skip Setup for Now
                    </button>
                </form>
                @if($hasClinics && $hasServices)
                    <a href="{{ route('app.dashboard') }}" class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-center text-sm sm:text-base touch-manipulation">
                        Go to Dashboard
                    </a>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

