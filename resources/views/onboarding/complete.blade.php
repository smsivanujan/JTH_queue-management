@extends('layouts.tenant')

@section('title', 'Setup Complete - SmartQueue')

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
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    .scale-in {
        animation: scaleIn 0.5s ease-out;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-50">
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 sm:p-8 lg:p-12 fade-in">
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg scale-in">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Setup Complete! 🎉</h1>
                <p class="text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">
                    You're all set! Your queue management system is ready to use.
                </p>
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8 mb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6">What You've Created:</h2>
                <div class="space-y-4">
                    @if($clinic)
                        <div class="flex items-center gap-4 p-4 bg-white rounded-lg border border-gray-200">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $clinic->name }}</div>
                                <div class="text-sm text-gray-600">Location</div>
                            </div>
                        </div>
                    @endif

                    @if($service)
                        <div class="flex items-center gap-4 p-4 bg-white rounded-lg border border-gray-200">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $service->name }}</div>
                                <div class="text-sm text-gray-600">Service Queue ({{ ucfirst($service->type) }})</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Next Steps -->
            <div class="mb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4">What's Next?</h2>
                <div class="space-y-3 text-sm sm:text-base text-gray-600">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div>
                            <strong class="text-gray-900">Manage your queue:</strong> Open the service queue to start managing customer flow.
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div>
                            <strong class="text-gray-900">Add more locations:</strong> Create additional locations from the dashboard.
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <div>
                            <strong class="text-gray-900">Add more services:</strong> Create additional service queues as needed.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-4 pt-6 border-t border-gray-200">
                @if($service)
                    <a href="{{ route('app.service.show', $service) }}" class="px-6 sm:px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:shadow-xl hover:scale-105 transition-all text-center text-sm sm:text-base touch-manipulation">
                        Open Service Queue
                    </a>
                @endif
                <a href="{{ route('app.dashboard') }}" class="px-6 sm:px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center text-sm sm:text-base touch-manipulation">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </main>
</div>
@endsection

