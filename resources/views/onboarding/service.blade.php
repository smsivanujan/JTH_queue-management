@extends('layouts.tenant')

@section('title', 'Create First Service - SmartQueue')

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
    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sm:p-8 lg:p-10 fade-in">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-600 text-white rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base font-semibold text-gray-500 hidden sm:inline">Location</span>
                    </div>
                    <div class="flex-1 h-1 bg-gray-200 mx-4 rounded-full">
                        <div class="h-1 bg-blue-600 rounded-full" style="width: 66%"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm sm:text-base">2</div>
                        <span class="text-sm sm:text-base font-semibold text-gray-900">Create Service</span>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Create Your First Service</h1>
                <p class="text-base sm:text-lg text-gray-600">
                    Set up a service queue to start managing your queues.
                </p>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="font-semibold mb-2 text-sm sm:text-base">Please fix the following errors:</div>
                    <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('app.onboarding.service.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Service Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           autofocus
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="e.g., Customer Service, Order Pickup, Consultation">
                    <p class="mt-2 text-xs sm:text-sm text-gray-500">
                        Choose a descriptive name for this service queue.
                    </p>
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Token Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Token Type <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Sequential Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer mb-3 transition-all hover:bg-gray-50 {{ old('type', 'sequential') === 'sequential' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                        <input type="radio" 
                               name="type" 
                               value="sequential" 
                               {{ old('type', 'sequential') === 'sequential' ? 'checked' : '' }}
                               class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 mb-1">Sequential</div>
                            <div class="text-xs sm:text-sm text-gray-600">Single numbers displayed one at a time (e.g., 1, 2, 3, 4...)</div>
                            <div class="mt-2 text-xs text-gray-500">Best for: Single-line queues, first-come-first-served systems</div>
                        </div>
                    </label>

                    <!-- Range Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50 {{ old('type') === 'range' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                        <input type="radio" 
                               name="type" 
                               value="range" 
                               {{ old('type') === 'range' ? 'checked' : '' }}
                               class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 mb-1">Range</div>
                            <div class="text-xs sm:text-sm text-gray-600">Start and end numbers displayed together (e.g., 1-5, 6-10, 11-15...)</div>
                            <div class="mt-2 text-xs text-gray-500">Best for: Batch processing, grouped tickets, multi-service queues</div>
                        </div>
                    </label>
                    
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Access Password (Optional)
                    </label>
                    <input type="text" 
                           id="password" 
                           name="password" 
                           value="{{ old('password') }}"
                           minlength="4"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Leave empty if you don't want password protection">
                    <p class="mt-2 text-xs sm:text-sm text-gray-500">
                        Optional: Set a password to protect access to this service queue. Minimum 4 characters.
                    </p>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('app.onboarding.clinic') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation cursor-pointer" style="pointer-events: auto;">
                        ← Back
                    </a>
                    <button type="submit" class="px-6 sm:px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-sm sm:text-base touch-manipulation cursor-pointer" style="pointer-events: auto;">
                        Complete Setup →
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection

