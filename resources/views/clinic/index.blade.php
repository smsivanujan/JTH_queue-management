@extends('layouts.tenant')

@section('title', 'Clinic Management - SmartQueue Hospital')

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
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Clinic Management</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Manage your organization's clinics</p>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <a href="{{ route('app.dashboard') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                        Back to Dashboard
                    </a>
                    @if($canCreateMore)
                        <a href="{{ route('app.clinic.create') }}" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden xs:inline">Add Clinic</span>
                        </a>
                    @else
                        @unless(auth()->check() && auth()->user()->isSuperAdmin())
                            <div class="relative group">
                                <button disabled class="px-3 sm:px-4 py-2 bg-gray-400 text-white rounded-lg font-semibold cursor-not-allowed text-xs sm:text-sm opacity-60">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="hidden xs:inline">Add Clinic</span>
                                </button>
                                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block w-64">
                                    <div class="bg-gray-900 text-white text-xs sm:text-sm rounded-lg py-2 px-3 shadow-lg">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <div class="font-semibold mb-1">Clinic Limit Reached</div>
                                                <div class="mb-2"><strong>Reason:</strong> Plan limit</div>
                                                <div class="text-xs opacity-90 mb-2">You've reached the maximum number of clinics allowed on your current plan ({{ $currentCount }} of {{ $maxClinics == -1 ? 'unlimited' : $maxClinics }}).</div>
                                                <div><a href="{{ route('app.plans.index') }}" class="underline font-semibold text-blue-300 hover:text-blue-200">Upgrade your plan</a> to add more clinics.</div>
                                            </div>
                                        </div>
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                            <div class="border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Super Admin can always add clinics --}}
                            <a href="{{ route('app.clinic.create') }}" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-xs sm:text-sm touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="hidden xs:inline">Add Clinic</span>
                            </a>
                        @endunless
                    @endif
                </div>
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

        <!-- Clinic Limit Info -->
        @if(!$canCreateMore && $maxClinics !== null && !(auth()->check() && auth()->user()->isSuperAdmin()))
            <div class="mb-4 sm:mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg fade-in">
                <div class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1 text-xs sm:text-sm">
                        <div class="font-semibold mb-1">Clinic Limit Reached</div>
                        <div>You have reached your clinic limit ({{ $currentCount }} of {{ $maxClinics == -1 ? 'unlimited' : $maxClinics }}). <a href="{{ route('app.plans.index') }}" class="underline font-semibold hover:text-yellow-900">Upgrade your plan</a> to add more clinics.</div>
                    </div>
                </div>
            </div>
        @endif

        @if($clinics->isEmpty())
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 p-8 sm:p-12 text-center fade-in">
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">No Clinics Yet</h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 max-w-md mx-auto">Clinics are locations where you manage patient queues and services. Add your first clinic to get started.</p>
                @if($canCreateMore)
                    <a href="{{ route('app.clinic.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all touch-manipulation text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Your First Clinic
                    </a>
                @else
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 max-w-md mx-auto">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-amber-900 mb-1">Clinic Limit Reached</p>
                                <p class="text-xs text-amber-800 mb-2">You've reached the maximum number of clinics allowed on your current plan ({{ $currentCount }} of {{ $maxClinics == -1 ? 'unlimited' : $maxClinics }}).</p>
                                <p class="text-xs text-amber-700">Upgrade your plan to add more clinic locations to your organization.</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('app.plans.index') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all touch-manipulation text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Upgrade Plan to Add More Clinics
                    </a>
                @endif
            </div>
        @else
            <!-- Clinic List -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden fade-in">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-cyan-50">
                            <tr>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden sm:table-cell">Status</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">Created</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($clinics as $clinic)
                                @php
                                    // Clinics are always active (no soft delete implemented)
                                    $isActive = true;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $clinic->name }}</div>
                                                <!-- Mobile: Show status -->
                                                <div class="sm:hidden mt-1 flex items-center gap-2 flex-wrap">
                                                    @if($isActive)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                                            Disabled
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        @if($isActive)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <div class="text-xs sm:text-sm text-gray-600">{{ $clinic->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @admin
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('app.clinic.edit', $clinic) }}" class="text-blue-600 hover:text-blue-800 font-semibold px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg hover:bg-blue-50 transition-colors touch-manipulation text-xs sm:text-sm">
                                                Edit
                                            </a>
                                            <form action="{{ route('app.clinic.destroy', $clinic) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this clinic? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold px-2 sm:px-3 py-1 sm:py-1.5 rounded-lg hover:bg-red-50 transition-colors touch-manipulation text-xs sm:text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
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
                                                            <div class="text-xs opacity-90 mb-2">Clinic management actions (Edit, Delete) require Administrator role. Your current role doesn't have permission to manage clinics.</div>
                                                            <div class="text-xs opacity-75">Contact your administrator if you need clinic management access.</div>
                                                        </div>
                                                    </div>
                                                    <div class="absolute top-0 right-4 -mt-1">
                                                        <div class="border-4 border-transparent border-t-gray-900"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        @endadmin
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Clinic Limit Info Footer -->
            @if(!$canCreateMore && $maxClinics !== null)
                <div class="mt-4 sm:mt-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>You have {{ $currentCount }} of {{ $maxClinics == -1 ? 'unlimited' : $maxClinics }} clinics. <a href="{{ route('app.plans.index') }}" class="underline font-semibold hover:text-blue-900">Upgrade your plan</a> to add more.</span>
                    </div>
                </div>
            @endif
        @endif
    </main>
</div>
@endsection

