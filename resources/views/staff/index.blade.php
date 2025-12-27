@extends('layouts.tenant')

@section('title', 'Staff Management - SmartQueue')

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
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Staff Management</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Manage your organization's staff members</p>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto">
                    <a href="{{ route('app.dashboard') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        Back to Dashboard
                    </a>
                    @admin
                    @if($canAddMore)
                        <a href="{{ route('app.staff.create') }}" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden xs:inline">Add Staff</span>
                        </a>
                    @else
                        @unless(auth()->check() && auth()->user()->isSuperAdmin())
                            <div class="relative group">
                                <button disabled class="px-3 sm:px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed text-xs sm:text-sm opacity-60">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="hidden xs:inline">Add Staff</span>
                                </button>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block w-72 z-50">
                                    <div class="bg-gray-900 text-white text-xs sm:text-sm rounded-lg py-2 px-3 shadow-lg">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <div class="font-semibold mb-1">Staff Limit Reached</div>
                                                <div class="mb-2"><strong>Reason:</strong> Plan limit</div>
                                                <div class="text-xs opacity-90 mb-2">You've reached the maximum number of staff members allowed on your current plan ({{ $currentCount }} of {{ $maxUsers == -1 ? 'unlimited' : $maxUsers }}).</div>
                                                <div><a href="{{ route('app.plans.index') }}" class="underline font-semibold text-blue-300 hover:text-blue-200">Upgrade your plan</a> to add more staff members.</div>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                            <div class="border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Super Admin can always add staff --}}
                            <a href="{{ route('app.staff.create') }}" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="hidden xs:inline">Add Staff</span>
                            </a>
                        @endunless
                    @endif
                    @endadmin
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        <!-- Staff Limit Info -->
        @if(!$canAddMore && $maxUsers !== null && !(auth()->check() && auth()->user()->isSuperAdmin()))
            <div class="mb-4 sm:mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1 text-xs sm:text-sm">
                        <div class="font-semibold mb-1">Staff Limit Reached</div>
                        <div>You have reached your staff limit ({{ $currentCount }} of {{ $maxUsers == -1 ? 'unlimited' : $maxUsers }}). <a href="{{ route('app.plans.index') }}" class="underline font-semibold hover:text-yellow-900">Upgrade your plan</a> to add more staff members.</div>
                    </div>
                </div>
            </div>
        @endif

        @if($staff->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 p-12 text-center fade-in">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">No Staff Members</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 max-w-md mx-auto">Staff members are users who can access your organization's system with different roles and permissions.</p>
                @if($canAddMore)
                    <a href="{{ route('app.staff.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all touch-manipulation text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Your First Staff Member
                    </a>
                @else
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 max-w-md mx-auto">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-amber-900 mb-1">Staff Limit Reached</p>
                                <p class="text-xs text-amber-800 mb-2">You've reached the maximum number of staff members allowed on your current plan ({{ $currentCount }} of {{ $maxUsers == -1 ? 'unlimited' : $maxUsers }}).</p>
                                <p class="text-xs text-amber-700">Upgrade your plan to add more staff members to your organization.</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('app.plans.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all touch-manipulation text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Upgrade Plan to Add More Staff
                    </a>
                @endif
            </div>
        @else
            <!-- Staff List -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden fade-in">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">Email</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">Permissions</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">Joined</th>
                                @admin
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                                @endadmin
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($staff as $member)
                                @php
                                    $role = $member->pivot->role;
                                    $isActive = $member->pivot->is_active ?? true;
                                    $roleLabel = \App\Helpers\RoleHelper::roleLabel($role);
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'reception' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'doctor' => 'bg-green-100 text-green-800 border-green-200',
                                        'lab' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                        'viewer' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    ];
                                    $roleColor = $roleColors[$role] ?? $roleColors['viewer'];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </div>
                                            <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                                                <div class="text-xs sm:text-sm font-semibold text-gray-900 truncate">{{ $member->name }}</div>
                                                <!-- Mobile: Show email on same line -->
                                                <div class="sm:hidden mt-0.5">
                                                    <div class="text-xs text-gray-500 truncate">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="text-xs sm:text-sm text-gray-600">{{ $member->email }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex flex-col gap-1.5 sm:gap-2">
                                            <span class="px-2 sm:px-3 py-1 inline-flex text-xs font-semibold rounded-full border-2 {{ $roleColor }} w-fit">
                                                {{ $roleLabel }}
                                            </span>
                                            <!-- Mobile: Show short description -->
                                            <div class="md:hidden">
                                                <span class="text-xs text-gray-500" title="{{ \App\Helpers\RoleHelper::roleDescription($role) }}">
                                                    {{ Str::limit(\App\Helpers\RoleHelper::roleDescription($role), 50) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 hidden md:table-cell">
                                        <div class="max-w-xs">
                                            <ul class="space-y-1">
                                                @foreach(\App\Helpers\RoleHelper::rolePermissions($role) as $permission)
                                                    <li class="text-xs text-gray-600 flex items-start">
                                                        <svg class="w-3 h-3 mr-1.5 mt-0.5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span>{{ $permission }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        @if($isActive)
                                            <span class="px-2 sm:px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 sm:px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-600 hidden lg:table-cell">
                                        {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    @admin
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('app.staff.edit', $member) }}" class="text-blue-600 hover:text-blue-900 transition-colors touch-manipulation px-2 py-1 rounded hover:bg-blue-50" title="Edit">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('app.staff.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to {{ $isActive ? 'disable' : 'remove' }} this staff member?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors touch-manipulation px-2 py-1 rounded hover:bg-red-50" title="{{ $isActive ? 'Disable' : 'Remove' }}">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @else
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium relative group">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-gray-400 text-xs font-medium">View Only</span>
                                            <svg class="w-4 h-4 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="absolute right-0 top-full mt-2 hidden group-hover:block w-72 z-50">
                                            <div class="bg-gray-900 text-white text-xs sm:text-sm rounded-lg py-2 px-3 shadow-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <div>
                                                        <div class="font-semibold mb-1">Restricted: Administrator Only</div>
                                                        <div class="mb-2"><strong>Reason:</strong> Role restriction</div>
                                                        <div class="text-xs opacity-90 mb-2">Staff management actions (Edit, Remove) require Administrator role. Your current role doesn't have permission to manage staff members.</div>
                                                        <div class="text-xs opacity-75">Contact your administrator if you need staff management access.</div>
                                                    </div>
                                                </div>
                                                <div class="absolute top-0 right-4 -mt-1">
                                                    <div class="border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    @endadmin
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="mt-4 sm:mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                @php
                    $stats = [
                        'admin' => $staff->where('pivot.role', 'admin')->where('pivot.is_active', true)->count(),
                        'reception' => $staff->where('pivot.role', 'reception')->where('pivot.is_active', true)->count(),
                        'doctor' => $staff->where('pivot.role', 'doctor')->where('pivot.is_active', true)->count(),
                        'lab' => $staff->where('pivot.role', 'lab')->where('pivot.is_active', true)->count(),
                        'viewer' => $staff->where('pivot.role', 'viewer')->where('pivot.is_active', true)->count(),
                    ];
                @endphp
                @foreach(['admin', 'reception', 'doctor', 'lab', 'viewer'] as $roleKey)
                    <div class="bg-white rounded-lg sm:rounded-xl shadow-md p-3 sm:p-4 border border-gray-200">
                        <div class="text-center">
                            <p class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">{{ \App\Helpers\RoleHelper::roleLabel($roleKey) }}</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats[$roleKey] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</div>
@endsection

