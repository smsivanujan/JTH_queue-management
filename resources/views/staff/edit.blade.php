@extends('layouts.app')

@section('title', 'Edit Staff - SmartQueue Hospital')

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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Edit Staff Member</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Update staff member information</p>
                </div>
                <a href="{{ route('app.staff.index') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Staff List
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 lg:p-8 fade-in">
                <form action="{{ route('app.staff.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="mb-4 sm:mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <div class="font-semibold mb-2 text-sm sm:text-base">Please fix the following errors:</div>
                            <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Name -->
                    <div class="mb-4 sm:mb-6">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $staff->name) }}"
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
                               value="{{ old('email', $staff->email) }}"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-base touch-manipulation"
                               placeholder="Enter email address">
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
                                <option value="{{ $roleKey }}" {{ old('role', $userRole) === $roleKey ? 'selected' : '' }}>
                                    {{ $roleLabels[$roleKey] ?? ucfirst($roleKey) }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Role Description Display -->
                        <div id="role-description-container" class="mt-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                                <h4 class="text-sm font-semibold text-blue-900 mb-2">Role Capabilities</h4>
                                <p class="text-xs sm:text-sm text-blue-800 mb-3" id="role-description-text"></p>
                                <div id="role-permissions-list" class="space-y-1"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4 sm:mb-6">
                        <label class="flex items-center space-x-3 cursor-pointer touch-manipulation">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $isActive) ? 'checked' : '' }}
                                   class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm font-semibold text-gray-700">Active Status</span>
                        </label>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Inactive staff members cannot access the system</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-gray-200">
                        <a href="{{ route('app.staff.index') }}" class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-sm sm:text-base touch-manipulation">
                            Update Staff Member
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar - Password Reset -->
            <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                <!-- Password Reset Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 fade-in">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4">Reset Password</h3>
                    <form action="{{ route('app.staff.reset-password', $staff) }}" method="POST" id="resetPasswordForm">
                        @csrf
                        <div class="mb-3 sm:mb-4">
                            <label for="new_password" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">New Password <span class="text-red-500">*</span></label>
                            <input type="password" 
                                   id="new_password" 
                                   name="password" 
                                   required
                                   class="w-full px-3 sm:px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-sm sm:text-base touch-manipulation"
                                   placeholder="Enter new password">
                        </div>
                        <div class="mb-3 sm:mb-4">
                            <label for="new_password_confirmation" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" 
                                   id="new_password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   class="w-full px-3 sm:px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 text-sm sm:text-base touch-manipulation"
                                   placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-lg font-semibold hover:shadow-lg hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                            Reset Password
                        </button>
                    </form>
                </div>

                <!-- User Info Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-4 sm:p-6 fade-in">
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-3 sm:mb-4">User Information</h3>
                    <div class="space-y-2 sm:space-y-3 text-xs sm:text-sm">
                        <div>
                            <p class="text-gray-600 font-semibold">Current Role</p>
                            <p class="text-gray-900 font-bold">{{ \App\Helpers\RoleHelper::roleLabel($userRole) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">Status</p>
                            <p class="text-gray-900 font-bold">
                                @if($isActive)
                                    <span class="text-green-600">Active</span>
                                @else
                                    <span class="text-red-600">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 font-semibold">Joined</p>
                            <p class="text-gray-900 font-bold">
                                @php
                                    $joinedAt = $staff->tenants()->where('tenants.id', $tenant->id)->first()->pivot->joined_at ?? null;
                                @endphp
                                {{ $joinedAt ? \Carbon\Carbon::parse($joinedAt)->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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

    // Function to update role description display
    function updateRoleDescription(role) {
        const descriptionText = document.getElementById('role-description-text');
        const permissionsList = document.getElementById('role-permissions-list');
        
        if (role && roleData[role]) {
            const data = roleData[role];
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
        }
    }

    // Show role description and permissions on selection
    document.getElementById('role').addEventListener('change', function() {
        updateRoleDescription(this.value);
    });
    
    // Initialize with current role
    const roleSelect = document.getElementById('role');
    if (roleSelect.value) {
        updateRoleDescription(roleSelect.value);
    }
</script>
@endpush
@endsection

