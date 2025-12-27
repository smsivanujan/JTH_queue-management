@extends('layouts.tenant')

@section('title', 'Edit Service - SmartQueue')

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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Edit Service</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Update service settings</p>
                </div>
                <a href="{{ route('app.services.list') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Services
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-6 sm:p-8 fade-in">
            <form action="{{ route('app.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

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

                <!-- Service Name -->
                <div class="mb-4 sm:mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Service Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $service->name) }}"
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="e.g., Customer Service, Order Pickup, Consultation">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Token Type -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Token Type <span class="text-red-500">*</span></label>
                    
                    <!-- Sequential Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer mb-3 transition-all hover:bg-gray-50 {{ old('type', $service->type) === 'sequential' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                        <input type="radio" 
                               name="type" 
                               value="sequential" 
                               {{ old('type', $service->type) === 'sequential' ? 'checked' : '' }}
                               class="mt-1 mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900 mb-1">Sequential</div>
                            <div class="text-xs sm:text-sm text-gray-600">Single numbers displayed one at a time (e.g., 1, 2, 3, 4...)</div>
                            <div class="mt-2 text-xs text-gray-500">Best for: Single-line queues, first-come-first-served systems</div>
                        </div>
                    </label>

                    <!-- Range Option -->
                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:bg-gray-50 {{ old('type', $service->type) === 'range' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                        <input type="radio" 
                               name="type" 
                               value="range" 
                               {{ old('type', $service->type) === 'range' ? 'checked' : '' }}
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
                <div class="mb-4 sm:mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Change Access Password</label>
                    <input type="text" 
                           id="password" 
                           name="password" 
                           value="{{ old('password') }}"
                           minlength="4"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Leave empty to keep current password">
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Minimum 4 characters. Leave empty to keep the current password. Enter a new password to change it.</p>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-4 sm:mb-6">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-700 mb-1">Status</div>
                            <p class="text-xs sm:text-sm text-gray-500">Set service status. Inactive services cannot be accessed.</p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700" id="status-label">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('app.services.list') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-sm sm:text-base touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed" onclick="this.disabled=true; this.form.submit();">
                        Update Service
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

@push('scripts')
<script>
    // Update status label when toggle changes
    document.getElementById('is_active').addEventListener('change', function() {
        const label = document.getElementById('status-label');
        label.textContent = this.checked ? 'Active' : 'Inactive';
    });
</script>
@endpush
@endsection

