@extends('layouts.tenant')

@section('title', 'Usage Metrics - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .metric-card {
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }
    .metric-card:nth-child(1) { animation-delay: 0.1s; }
    .metric-card:nth-child(2) { animation-delay: 0.2s; }
    .metric-card:nth-child(3) { animation-delay: 0.3s; }
    .metric-card:nth-child(4) { animation-delay: 0.4s; }
    .metric-card:nth-child(5) { animation-delay: 0.5s; }
    .metric-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Usage Metrics</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Track your queue management activity</p>
                </div>
                <a href="{{ route('app.dashboard') }}" class="px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-md text-xs sm:text-sm touch-manipulation">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Queues Opened Today -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Queues Opened</h3>
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ number_format($metrics['queues_opened_today']) }}</div>
                <p class="text-xs text-gray-500">Today</p>
                <p class="text-xs text-gray-400 mt-1">({{ number_format($metrics['queues_opened_week']) }} this week)</p>
            </div>

            <!-- Tokens Served Today -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Tokens Served</h3>
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ number_format($metrics['tokens_served_today']) }}</div>
                <p class="text-xs text-gray-500">Today</p>
                <p class="text-xs text-gray-400 mt-1">({{ number_format($metrics['tokens_served_week']) }} this week)</p>
            </div>

            <!-- Active Screens -->
            <div class="metric-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Active Screens</h3>
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ number_format($metrics['active_screens']) }}</div>
                <p class="text-xs text-gray-500">Currently displaying</p>
            </div>
        </div>

        <!-- Usage Trends -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 lg:p-8 border border-gray-100 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Usage Trends (Last 7 Days)</h3>
            
            @if(count($metrics['usage_trends']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-right text-xs font-semibold text-gray-700 uppercase">Queues Opened</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-right text-xs font-semibold text-gray-700 uppercase">Tokens Served</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($metrics['usage_trends'] as $trend)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-gray-900 font-medium">{{ $trend['date'] }}</td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-right text-gray-700">{{ number_format($trend['queues']) }}</td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-right text-gray-700">{{ number_format($trend['tokens']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">No usage data available</p>
            @endif
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">About These Metrics</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Queues Opened:</strong> Number of queue screens accessed/created</li>
                            <li><strong>Tokens Served:</strong> Total number of tokens called/displayed</li>
                            <li><strong>Active Screens:</strong> Display screens currently showing queue information</li>
                            <li>All metrics are calculated in real-time</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
