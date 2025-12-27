@extends('layouts.tenant')

@section('title', 'Add Clinic - SmartQueue')

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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Add Clinic</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Add a new clinic to your organization</p>
                </div>
                <a href="{{ route('app.clinic.index') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Clinic List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-6 sm:p-8 fade-in">
            <form action="{{ route('app.clinic.store') }}" method="POST">
                @csrf

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

                <!-- Name -->
                <div class="mb-4 sm:mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Clinic Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Enter clinic name">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Display Count -->
                <div class="mb-4 sm:mb-6">
                    <label for="display_count" class="block text-sm font-semibold text-gray-700 mb-2">Display Count</label>
                    <input type="number" 
                           id="display_count" 
                           name="display_count" 
                           value="{{ old('display_count', 1) }}"
                           min="1"
                           max="10"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Number of display screens">
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Number of display screens for this clinic (1-10). This determines how many queue displays can be shown simultaneously.</p>
                    @error('display_count')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-4 sm:mb-6">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-gray-700 mb-1">Status</div>
                            <p class="text-xs sm:text-sm text-gray-500">Set clinic status. Inactive clinics cannot be accessed.</p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700" id="status-label">Active</span>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4 sm:mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Access Password</label>
                    <input type="text" 
                           id="password" 
                           name="password" 
                           value="{{ old('password') }}"
                           minlength="4"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Leave empty for default (1234)">
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Minimum 4 characters. Leave empty to use default password (1234). This password is used to access the clinic's queue management.</p>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600 flex items-center">
                            <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('app.clinic.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-sm sm:text-base touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed" onclick="this.disabled=true; this.form.submit();">
                        Create Clinic
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

