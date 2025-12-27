@extends('layouts.tenant')

@section('title', 'Add Staff - SmartQueue')

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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Add Staff Member</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Add a new staff member to your organization</p>
                </div>
                <a href="{{ route('app.staff.index') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Staff List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 lg:p-8 fade-in">
            <form action="{{ route('app.staff.store') }}" method="POST">
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
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Enter full name">
                </div>

                <!-- Email -->
                <div class="mb-4 sm:mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Enter email address">
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">This email will be used for login</p>
                </div>

                <!-- Password -->
                <div class="mb-4 sm:mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Enter password">
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Minimum 8 characters</p>
                </div>

                <!-- Password Confirmation -->
                <div class="mb-4 sm:mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                           placeholder="Confirm password">
                </div>

                <!-- Role -->
                <div class="mb-4 sm:mb-6">
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select id="role" 
                            name="role" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 bg-white text-base touch-manipulation">
                        <option value="">Select a role</option>
                        @foreach($roles as $roleKey)
                            <option value="{{ $roleKey }}" {{ old('role') === $roleKey ? 'selected' : '' }}>
                                {{ $roleLabels[$roleKey] ?? ucfirst($roleKey) }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Role Description Display -->
                    <div id="role-description-container" class="mt-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Role Capabilities</h4>
                            <p class="text-xs sm:text-sm text-blue-800 mb-3" id="role-description-text"></p>
                            <div id="role-permissions-list" class="space-y-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-gray-200">
                    <a href="{{ route('app.staff.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-sm sm:text-base touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed" onclick="this.disabled=true; this.form.submit();">
                        Add Staff Member
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

@push('scripts')
<script>
    // Role descriptions and permissions
    const roleData = {
        @foreach($roles as $roleKey)
            '{{ $roleKey }}': {
                description: @json(\App\Helpers\RoleHelper::roleDescription($roleKey)),
                permissions: @json(\App\Helpers\RoleHelper::rolePermissions($roleKey))
            },
        @endforeach
    };

    // Show role description and permissions on selection
    document.getElementById('role').addEventListener('change', function() {
        const selectedRole = this.value;
        const container = document.getElementById('role-description-container');
        const descriptionText = document.getElementById('role-description-text');
        const permissionsList = document.getElementById('role-permissions-list');
        
        if (selectedRole && roleData[selectedRole]) {
            const data = roleData[selectedRole];
            descriptionText.textContent = data.description;
            
            // Clear and populate permissions
            permissionsList.innerHTML = '';
            if (data.permissions && data.permissions.length > 0) {
                data.permissions.forEach(permission => {
                    const item = document.createElement('div');
                    item.className = 'flex items-start text-sm text-blue-700';
                    item.innerHTML = `
                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>${permission}</span>
                    `;
                    permissionsList.appendChild(item);
                });
            }
            
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    });
    
    // Trigger on page load if role is pre-selected
    const roleSelect = document.getElementById('role');
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection

