@extends('layouts.tenant')

@section('title', 'Queue Management - SmartQueue')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        overflow-x: hidden; /* Prevent horizontal overflow on mobile */
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInScale {
        0% {
            opacity: 0;
            transform: scale(0.8) translateY(20px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);
        }
        50% {
            box-shadow: 0 0 40px rgba(34, 197, 94, 0.8);
        }
    }
    
    @keyframes numberUpdate {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .queue-card {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    .queue-card:nth-child(1) { animation-delay: 0.1s; }
    .queue-card:nth-child(2) { animation-delay: 0.2s; }
    .queue-card:nth-child(3) { animation-delay: 0.3s; }
    .queue-card:nth-child(4) { animation-delay: 0.4s; }
    .queue-card:nth-child(5) { animation-delay: 0.5s; }
    
    .token-display {
        font-weight: 900;
        letter-spacing: -0.02em;
        text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .number-update {
        animation: numberUpdate 0.5s ease-out;
    }
    
    .queue-active {
        animation: pulseGlow 2s ease-in-out infinite;
    }
    
    /* Ensure touch targets are at least 44px on mobile */
    @media (max-width: 640px) {
        button, a {
            min-height: 44px;
        }
        /* Better mobile spacing */
        .queue-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 overflow-x-hidden flex flex-col">
    <!-- Header -->
    <div class="bg-slate-800/90 backdrop-blur-md border-b border-slate-700 sticky top-0 z-40 shadow-lg flex-shrink-0">
        <div class="max-w-[1920px] mx-auto px-3 sm:px-6 lg:px-12 py-3 sm:py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold text-white break-words leading-tight">{{ $clinic->name }}</h1>
                    <p class="text-xs sm:text-sm lg:text-base text-slate-300 mt-1">Queue Management System</p>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    @canManageQueues
                        <!-- Queue Type Toggle -->
                        <div class="relative group">
                            <button 
                                type="button" 
                                onclick="toggleQueueType(event)"
                                class="px-3 sm:px-4 py-2 sm:py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-lg sm:rounded-xl font-semibold transition-all duration-200 text-xs sm:text-sm touch-manipulation flex items-center gap-2 border border-white/30"
                                title="Switch queue type">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <span class="hidden sm:inline">{{ $queue->isRangeType() ? 'Range' : 'Sequential' }}</span>
                            </button>
                            <div class="absolute top-full right-0 mt-2 hidden group-hover:block z-50 w-48">
                                <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
                                    <div class="font-semibold mb-1">Queue Type</div>
                                    <div class="break-words">Current: <strong>{{ $queue->isRangeType() ? 'Range' : 'Sequential' }}</strong>. Click to switch.</div>
                                    <div class="absolute top-0 right-4 -mt-1">
                                        <div class="border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanManageQueues
                    <a href="{{ route('app.dashboard') }}" class="w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg sm:rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg text-sm sm:text-base touch-manipulation text-center inline-flex items-center justify-center min-h-[44px]">
                        Exit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-3 sm:px-6 lg:px-12 py-3 sm:py-6 lg:py-8 xl:py-12">
        <div class="flex flex-wrap justify-center items-start gap-3 sm:gap-6 lg:gap-8 xl:gap-10 max-w-[1920px] w-full mx-auto">
            @for ($i = 1; $i <= $queue->display; $i++)
                @php
                    $subQueue = \App\Models\SubQueue::where('clinic_id', $clinic->id)
                                  ->where('queue_number', $i)->first();
                    
                    // Color schemes for different queues
                    $colorSchemes = [
                        [
                            'bg' => 'bg-gradient-to-br from-blue-600 to-blue-800',
                            'border' => 'border-blue-400',
                            'accent' => 'bg-blue-500',
                            'current' => 'text-blue-100',
                            'next' => 'text-blue-200',
                            'label' => 'text-blue-100',
                        ],
                        [
                            'bg' => 'bg-gradient-to-br from-purple-600 to-purple-800',
                            'border' => 'border-purple-400',
                            'accent' => 'bg-purple-500',
                            'current' => 'text-purple-100',
                            'next' => 'text-purple-200',
                            'label' => 'text-purple-100',
                        ],
                        [
                            'bg' => 'bg-gradient-to-br from-teal-600 to-teal-800',
                            'border' => 'border-teal-400',
                            'accent' => 'bg-teal-500',
                            'current' => 'text-teal-100',
                            'next' => 'text-teal-200',
                            'label' => 'text-teal-100',
                        ],
                        [
                            'bg' => 'bg-gradient-to-br from-amber-600 to-amber-800',
                            'border' => 'border-amber-400',
                            'accent' => 'bg-amber-500',
                            'current' => 'text-amber-100',
                            'next' => 'text-amber-200',
                            'label' => 'text-amber-100',
                        ],
                        [
                            'bg' => 'bg-gradient-to-br from-pink-600 to-pink-800',
                            'border' => 'border-pink-400',
                            'accent' => 'bg-pink-500',
                            'current' => 'text-pink-100',
                            'next' => 'text-pink-200',
                            'label' => 'text-pink-100',
                        ],
                    ];
                    $colors = $colorSchemes[($i-1) % count($colorSchemes)];
                    $labels = ['Urine Test', 'Full Blood Count', 'ESR'];
                    $queueLabel = ($queue->display == 3 && $clinic->id == 2) ? $labels[$i-1] : "Queue #{$i}";
                @endphp

                <div class="queue-card {{ $colors['bg'] }} rounded-xl sm:rounded-2xl lg:rounded-3xl shadow-2xl border-2 sm:border-4 {{ $colors['border'] }} p-4 sm:p-6 md:p-8 lg:p-10 hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-2 queue-active w-full sm:w-[48%] lg:w-[31%] max-w-md">
                    <!-- Queue Header -->
                    <div class="mb-3 sm:mb-6 lg:mb-8 xl:mb-10">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <h2 class="text-base sm:text-xl md:text-2xl lg:text-3xl font-bold {{ $colors['label'] }} break-words pr-2">{{ $queueLabel }}</h2>
                            <div class="w-3 h-3 sm:w-4 sm:h-4 {{ $colors['accent'] }} rounded-full animate-pulse flex-shrink-0"></div>
                        </div>
                        <div class="h-1 sm:h-1.5 w-12 sm:w-24 {{ $colors['accent'] }} rounded-full"></div>
                    </div>

                    @if($queue->isRangeType())
                        <!-- Range-based Display -->
                        <div class="bg-black/30 backdrop-blur-md rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-6 md:p-8 lg:p-12 mb-3 sm:mb-6 lg:mb-8 border-2 border-white/20 shadow-inner">
                            <p class="text-xs sm:text-sm lg:text-base font-bold {{ $colors['label'] }} uppercase tracking-wider sm:tracking-widest mb-2 sm:mb-3 lg:mb-4 xl:mb-6 text-center">Range Display</p>
                            <div class="relative overflow-hidden">
                                <div class="flex items-center justify-center gap-2 sm:gap-4">
                                    <span id="range-start-{{ $i }}" class="token-display text-3xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black {{ $colors['current'] }} block text-center number-display leading-none">
                                        {{ $subQueue->current_number ?? 1 }}
                                    </span>
                                    <span class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black {{ $colors['label'] }}">-</span>
                                    <span id="range-end-{{ $i }}" class="token-display text-3xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-black {{ $colors['current'] }} block text-center number-display leading-none">
                                        {{ $subQueue->next_number ?? 1 }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Range Inputs -->
                        <div class="bg-black/20 backdrop-blur-sm rounded-lg sm:rounded-xl lg:rounded-2xl p-3 sm:p-6 lg:p-8 mb-3 sm:mb-6 lg:mb-8 border border-white/10">
                            <p class="text-xs sm:text-sm font-semibold {{ $colors['label'] }} uppercase tracking-wider mb-2 sm:mb-3 text-center">Set Range</p>
                            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                <div>
                                    <label class="text-xs {{ $colors['label'] }} block mb-1">Start</label>
                                    <input type="number" id="range-start-input-{{ $i }}" min="1" value="{{ $subQueue->current_number ?? 1 }}" class="w-full px-2 sm:px-3 py-2 sm:py-3 text-lg sm:text-xl font-bold text-center rounded-lg bg-white/20 border border-white/30 {{ $colors['label'] }} focus:bg-white/30 focus:outline-none touch-manipulation">
                                </div>
                                <div>
                                    <label class="text-xs {{ $colors['label'] }} block mb-1">End</label>
                                    <input type="number" id="range-end-input-{{ $i }}" min="1" value="{{ $subQueue->next_number ?? 1 }}" class="w-full px-2 sm:px-3 py-2 sm:py-3 text-lg sm:text-xl font-bold text-center rounded-lg bg-white/20 border border-white/30 {{ $colors['label'] }} focus:bg-white/30 focus:outline-none touch-manipulation">
                                </div>
                            </div>
                        </div>

                        <!-- Range Action Button -->
                        <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                            @canManageQueues
                                <form id="range-form-{{ $i }}" action="{{ route('app.queues.range', ['clinic'=>$clinic->id,'queueNumber'=>$i]) }}" method="POST">
                                    @csrf
                                </form>
                                <button 
                                    type="button" 
                                    onclick="submitRangeAction({{ $i }}, event); return false;"
                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 sm:py-4 lg:py-5 px-1 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base lg:text-lg touch-manipulation min-h-[44px] flex items-center justify-center cursor-pointer"
                                    style="pointer-events: auto; z-index: 10;">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 flex-shrink-0 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                    <span class="hidden sm:inline">Call Range</span>
                                    <span class="sm:hidden">Call</span>
                                </button>
                            @else
                                <div class="bg-blue-500/20 border-2 border-blue-400/50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                                    <p class="text-blue-100 font-semibold text-xs sm:text-sm break-words">Read-only access</p>
                                    <p class="text-blue-100/80 text-xs mb-2 break-words px-1">You can view the queue but cannot manage it.</p>
                                </div>
                            @endcanManageQueues
                        </div>
                    @else
                        <!-- Sequential Display -->
                        <div class="bg-black/30 backdrop-blur-md rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-6 md:p-8 lg:p-12 mb-3 sm:mb-6 lg:mb-8 border-2 border-white/20 shadow-inner">
                            <p class="text-xs sm:text-sm lg:text-base font-bold {{ $colors['label'] }} uppercase tracking-wider sm:tracking-widest mb-2 sm:mb-3 lg:mb-4 xl:mb-6 text-center">Current Number</p>
                            <div class="relative overflow-hidden">
                                <span id="current-number-{{ $i }}" class="token-display text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl 2xl:text-[12rem] font-black {{ $colors['current'] }} block text-center number-display leading-none break-all">
                                    {{ $subQueue->current_number ?? 1 }}
                                </span>
                            </div>
                        </div>

                        <!-- Next Number Display -->
                        <div class="bg-black/20 backdrop-blur-sm rounded-lg sm:rounded-xl lg:rounded-2xl p-3 sm:p-6 lg:p-8 mb-3 sm:mb-6 lg:mb-8 border border-white/10">
                            <p class="text-xs sm:text-sm font-semibold {{ $colors['label'] }} uppercase tracking-wider mb-2 sm:mb-3 text-center">Next Number</p>
                            <span id="next-number-{{ $i }}" class="token-display text-2xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black {{ $colors['next'] }} block text-center number-display leading-none break-all">
                                {{ $subQueue->next_number ?? 2 }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 sm:space-y-3 lg:space-y-4">
                            @canManageQueues
                                <!-- Primary Actions Row (only visible to users with queue management roles) -->
                                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                            <form id="next-form-{{ $i }}" action="{{ route('app.queues.next', ['clinic'=>$clinic->id,'queueNumber'=>$i]) }}" method="POST">
                                @csrf
                            </form>
                            <button 
                                type="button" 
                                onclick="submitQueueAction('next', {{ $i }}, event); return false;"
                                class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 sm:py-4 lg:py-5 px-1 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base lg:text-lg touch-manipulation min-h-[44px] flex items-center justify-center cursor-pointer"
                                style="pointer-events: auto; z-index: 10;">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 flex-shrink-0 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <span class="hidden sm:inline">Next</span>
                            </button>

                            <form id="previous-form-{{ $i }}" action="{{ route('app.queues.previous', ['clinic'=>$clinic->id,'queueNumber'=>$i]) }}" method="POST">
                                @csrf
                            </form>
                            <button 
                                type="button" 
                                onclick="submitQueueAction('previous', {{ $i }}, event); return false;"
                                class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold py-3 sm:py-4 lg:py-5 px-1 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base lg:text-lg touch-manipulation min-h-[44px] flex items-center justify-center cursor-pointer"
                                style="pointer-events: auto; z-index: 10;">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 flex-shrink-0 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                                </svg>
                                <span class="hidden sm:inline">Prev</span>
                            </button>

                            <form id="reset-form-{{ $i }}" action="{{ route('app.queues.reset', ['clinic'=>$clinic->id,'queueNumber'=>$i]) }}" method="POST">
                                @csrf
                            </form>
                            <button 
                                type="button" 
                                onclick="submitQueueAction('reset', {{ $i }}, event); return false;"
                                class="bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-3 sm:py-4 lg:py-5 px-1 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl text-xs sm:text-sm md:text-base lg:text-lg touch-manipulation min-h-[44px] flex items-center justify-center cursor-pointer"
                                style="pointer-events: auto; z-index: 10;">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 flex-shrink-0 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="hidden sm:inline">Reset</span>
                            </button>
                        </div>
                        @else
                            <!-- View-only message for users without management permissions -->
                            <div class="bg-blue-500/20 border-2 border-blue-400/50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center relative group">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-200 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-blue-100 font-semibold text-xs sm:text-sm break-words">Read-only access</p>
                                </div>
                                <p class="text-blue-100/80 text-xs mb-2 break-words px-1">You can view the queue but cannot manage it.</p>
                                @php
                                    $userRole = auth()->user()->getCurrentRole();
                                    $allowedRoles = ['admin', 'reception', 'doctor'];
                                    $roleLabel = \App\Helpers\RoleHelper::roleLabel($userRole);
                                @endphp
                                <div class="mt-2 pt-2 border-t border-blue-400/30">
                                    <p class="text-blue-100/70 text-xs break-words px-1">
                                        <strong>Your role:</strong> {{ $roleLabel }}<br>
                                        <strong>Required roles:</strong> Administrator, Reception, or Doctor
                                    </p>
                                </div>
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 hidden group-hover:block w-72 max-w-[calc(100vw-2rem)] z-50">
                                    <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-lg">
                                        <div class="font-semibold mb-1">Queue Management Restricted</div>
                                        <div class="break-words">Your current role ({{ $roleLabel }}) does not have permission to manage queues. Only Administrator, Reception, and Doctor roles can control queue flow. Contact your administrator to change your role.</div>
                                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -mt-1">
                                            <div class="border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endcanManageQueues
                        </div>
                    @endif

                    <!-- Secondary Actions Row (shows for both sequential and range types) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3 mt-2 sm:mt-3 lg:mt-4">
                            <button 
                                type="button" 
                                onclick="openSecondScreen()"
                                class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold py-3 sm:py-3 lg:py-4 px-3 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl border border-white/30 text-xs sm:text-sm md:text-base touch-manipulation min-h-[44px] flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5 flex-shrink-0 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="hidden sm:inline">Second Screen</span>
                                <span class="sm:hidden">Screen</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="showPairingQR()"
                                class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold py-3 sm:py-3 lg:py-4 px-3 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl border border-white/30 text-xs sm:text-sm md:text-base touch-manipulation min-h-[44px] flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5 flex-shrink-0 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                <span class="hidden sm:inline">Show QR Code</span>
                                <span class="sm:hidden">QR Code</span>
                            </button>
                            <button 
                                type="button" 
                                onclick="recallSpeech()"
                                class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold py-3 sm:py-3 lg:py-4 px-3 sm:px-4 rounded-lg sm:rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl border border-white/30 text-xs sm:text-sm md:text-base touch-manipulation col-span-1 sm:col-span-2 min-h-[44px] flex items-center justify-center">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5 flex-shrink-0 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414-1.414m-1.414-2.828a9 9 0 010-12.728"/>
                                </svg>
                                Recall
                            </button>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/screenHeartbeat.js') }}"></script>
<script>
    // Queue configuration
    window.queueConfig = {
        clinicId: {{ $clinic->id }},
        queueType: @json($queue->type ?? 'sequential'),
        isRangeType: @json($queue->isRangeType() ?? false)
    };

    // Toggle queue type (sequential/range)
    function toggleQueueType(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const newType = window.queueConfig.isRangeType ? 'sequential' : 'range';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`/app/queues/{{ $clinic->id }}/type`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ type: newType })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || `HTTP error! status: ${res.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Reload page to show updated interface
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error toggling queue type:', error);
            alert('Failed to change queue type. Please try again.');
        });
    }

    // Submit range-based queue action
    function submitRangeAction(queueId, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const startInput = document.getElementById(`range-start-input-${queueId}`);
        const endInput = document.getElementById(`range-end-input-${queueId}`);
        
        if (!startInput || !endInput) {
            console.error(`Range inputs not found for queue ${queueId}`);
            return false;
        }

        const start = parseInt(startInput.value);
        const end = parseInt(endInput.value);

        if (isNaN(start) || isNaN(end) || start < 1 || end < 1) {
            alert('Please enter valid start and end values (minimum 1)');
            return false;
        }

        if (end < start) {
            alert('End value must be greater than or equal to start value');
            return false;
        }

        const form = document.getElementById(`range-form-${queueId}`);
        if (!form) {
            console.error(`Range form not found for queue ${queueId}`);
            return false;
        }

        const formData = new FormData(form);
        formData.append('start', start);
        formData.append('end', end);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token');
        
        // Visual feedback
        if (event && event.target) {
            const button = event.target.closest('button');
            if (button) {
                button.disabled = true;
                button.classList.add('scale-95', 'opacity-75');
                setTimeout(() => {
                    button.disabled = false;
                    button.classList.remove('scale-95', 'opacity-75');
                }, 500);
            }
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            let data;
            
            if (contentType && contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const text = await res.text();
                throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}`);
            }
            
            if (!res.ok) {
                throw new Error(data.message || `HTTP error! status: ${res.status}`);
            }
            
            return data;
        })
        .then(data => {
            if (data.success) {
                // Update display immediately
                const rangeStartEl = document.getElementById(`range-start-${queueId}`);
                const rangeEndEl = document.getElementById(`range-end-${queueId}`);
                if (rangeStartEl) rangeStartEl.textContent = start;
                if (rangeEndEl) rangeEndEl.textContent = end;
                
                // Speak the range
                if (typeof speakRange === 'function') {
                    speakRange(start, end);
                }
                
                // Refresh queue data
                fetchQueueLive();
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            console.error('Queue range action error:', error);
            alert('An error occurred. Please try again.');
        });

        return false;
    }

    // Queue button actions (sequential type)
    function submitQueueAction(action, queueId, event){
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        let form = document.getElementById(`${action}-form-${queueId}`);
        if (!form) {
            console.error(`Form ${action}-form-${queueId} not found`);
            return false;
        }
        
        let formData = new FormData(form);
        
        // Get CSRF token from meta tag if available
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token');
        
        // Visual feedback
        if(event && event.target) {
            const button = event.target.closest('button');
            if (button) {
                button.disabled = true;
                button.classList.add('scale-95', 'opacity-75');
                setTimeout(() => {
                    button.disabled = false;
                    button.classList.remove('scale-95', 'opacity-75');
                }, 500);
            }
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            let data;
            
            if (contentType && contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const text = await res.text();
                throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}`);
            }
            
            if (!res.ok) {
                throw new Error(data.message || `HTTP error! status: ${res.status}`);
            }
            
            return data;
        })
        .then(data => {
            if (data.success) {
                // Immediately refresh the queue display
                fetchQueueLive();
            } else {
                throw new Error(data.message || 'Action failed');
            }
        })
        .catch(err => {
            console.error('Queue action error:', err);
            console.error('Form action:', form.action);
            console.error('Form data:', formData);
            const errorMessage = err.message || 'An error occurred. Please try again.';
            alert('Error: ' + errorMessage);
        });
        
        return false;
    }

    // Text-to-speech (preserved)
    let currentMsg;
    function speakNumber(number){
        const msg = new SpeechSynthesisUtterance(`வரிசை எண் ${number} உள்ளே வரவும்`);
        msg.lang = 'ta-IN';
        msg.rate = 0.9;
        currentMsg = msg;

        let repeat=3;
        function repeatSpeech(){
            if(repeat>0){ window.speechSynthesis.cancel(); window.speechSynthesis.speak(msg); repeat--; }
        }
        msg.onend = repeatSpeech;
        repeatSpeech();
    }

    function speakRange(start, end){
        const msg = new SpeechSynthesisUtterance(`வரிசை எண் ${start} முதல் ${end} வரை உள்ளே வரவும்`);
        msg.lang = 'ta-IN';
        msg.rate = 0.9;
        currentMsg = msg;

        let repeat=3;
        function repeatSpeech(){
            if(repeat>0){ window.speechSynthesis.cancel(); window.speechSynthesis.speak(msg); repeat--; }
        }
        msg.onend = repeatSpeech;
        repeatSpeech();
    }

    function recallSpeech(){
        if(currentMsg){ 
            const text = currentMsg.text;
            const rangeMatch = text.match(/(\d+)\s*முதல்\s*(\d+)/);
            if(rangeMatch) {
                speakRange(rangeMatch[1], rangeMatch[2]);
            } else {
                const numMatch = text.match(/\d+/);
                if(numMatch) speakNumber(numMatch[0]);
            }
        }
        else alert("No speech has been made yet!");
    }

    // Second screen (preserved)
    let secondScreen = null;
    let clinicQueueName = @json($clinic->name);
    const clinicId = @json($clinic->id);

    async function showPairingQR(){
        try {
            // Check if screenHeartbeat is available
            if (typeof screenHeartbeat === 'undefined') {
                console.error('screenHeartbeat is not loaded');
                alert('Screen system not initialized. Please refresh the page and try again.');
                return;
            }
            
            // Register screen first if not already registered
            let pairingUrl = null;
            let screenToken = null;
            
            try {
                screenToken = await screenHeartbeat.register('queue', clinicId);
            } catch (regError) {
                console.error('Registration error:', regError);
                const errorMsg = regError.message || 'Failed to register screen';
                alert('Registration failed: ' + errorMsg + '\n\nPlease check:\n- Your subscription is active\n- Screen limit has not been reached\n- You have proper permissions');
                return;
            }
            
            if (screenToken) {
                // Get pairing URL from stored URLs (set during registration)
                pairingUrl = screenHeartbeat.getPairingUrl('queue', screenToken);
                
                if (!pairingUrl) {
                    console.error('Pairing URL not found after registration. Screen token:', screenToken);
                    console.log('Stored pairing URLs:', screenHeartbeat.pairingUrls);
                    console.log('Registration response should have included pairing_url');
                    alert('Pairing URL was not generated by the server. Please contact support.');
                    return;
                }
            } else {
                console.error('Screen registration failed - no token returned');
                alert('Failed to register screen. No token was returned from the server.');
                return;
            }
            
            if (pairingUrl) {
                // Open pairing page in new window (popup)
                window.open(pairingUrl, 'pairingQR', 'width=800,height=900,menubar=no,toolbar=no,location=no');
            } else {
                alert('Unable to generate pairing QR code. Please try again.');
            }
        } catch (error) {
            console.error('Error in showPairingQR:', error);
            alert('An error occurred while generating the QR code: ' + (error.message || error));
        }
    }

    async function openSecondScreen(){
        if(!secondScreen || secondScreen.closed){
            // Register screen with database and get signed URL
            let signedUrl = null;
            if (typeof screenHeartbeat !== 'undefined') {
                const screenToken = await screenHeartbeat.register('queue', clinicId);
                if (screenToken) {
                    // Get signed URL from stored URLs (set during registration)
                    signedUrl = screenHeartbeat.getSignedUrl('queue', screenToken);
                }
            }
            
            // Use signed URL if available (preferred for TV displays)
            if (signedUrl) {
                secondScreen = window.open(signedUrl, 'secondScreen', `width=${screen.availWidth},height=${screen.availHeight}`);
                if (secondScreen) {
                    secondScreen.moveTo(0,0); 
                    secondScreen.resizeTo(screen.availWidth, screen.availHeight);
                    localStorage.setItem('secondScreen', secondScreen.name);
                }
            } else {
                // Fallback: Open blank second screen (old method for backward compatibility)
                secondScreen = window.open('', 'secondScreen', `width=${screen.availWidth},height=${screen.availHeight}`);
                if (secondScreen) {
                    secondScreen.moveTo(0,0); 
                    secondScreen.resizeTo(screen.availWidth, screen.availHeight);
                    localStorage.setItem('secondScreen', secondScreen.name);
                }
                // Update content using old method
                updateSecondScreen();
            }
        } else {
            // Screen already open, update content if using old method (blank window)
            if (secondScreen.location && secondScreen.location.href === 'about:blank') {
                updateSecondScreen();
            }
        }
    }

    function updateSecondScreen(){
        if(!secondScreen) return;
        fetch(`{{ route('app.queues.fetchApi',['clinic'=>'__CLINIC_ID__']) }}`.replace('__CLINIC_ID__',clinicId))
        .then(res=>res.json())
        .then(data=>{
            let queueHtml='';
            const colorSchemes = [
                {bg: 'bg-gradient-to-br from-blue-600 to-blue-800', border: 'border-blue-400', current: 'text-blue-100', next: 'text-blue-200', label: 'text-blue-100', accent: 'bg-blue-500'},
                {bg: 'bg-gradient-to-br from-purple-600 to-purple-800', border: 'border-purple-400', current: 'text-purple-100', next: 'text-purple-200', label: 'text-purple-100', accent: 'bg-purple-500'},
                {bg: 'bg-gradient-to-br from-teal-600 to-teal-800', border: 'border-teal-400', current: 'text-teal-100', next: 'text-teal-200', label: 'text-teal-100', accent: 'bg-teal-500'},
                {bg: 'bg-gradient-to-br from-amber-600 to-amber-800', border: 'border-amber-400', current: 'text-amber-100', next: 'text-amber-200', label: 'text-amber-100', accent: 'bg-amber-500'},
                {bg: 'bg-gradient-to-br from-pink-600 to-pink-800', border: 'border-pink-400', current: 'text-pink-100', next: 'text-pink-200', label: 'text-pink-100', accent: 'bg-pink-500'},
            ];
            const labels=['Urine Test','Full Blood Count','ESR'];
            const isRangeType = @json($queue->isRangeType() ?? false);
            data.subQueues.forEach((sq,index)=>{
                const colors = colorSchemes[index%colorSchemes.length];
                const queueNum = sq.queue_number || (index+1);
                const label = (@json($queue->display)===3 && @json($clinic->id)===2) ? (labels[index]||`Queue`) : `Queue #${queueNum}`;
                
                if(isRangeType) {
                    // Range-based display
                    queueHtml+=`
                    <div class="${colors.bg} rounded-3xl shadow-2xl border-4 ${colors.border} p-10 w-full sm:w-[48%] lg:w-[31%] max-w-md">
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-3xl font-bold ${colors.label} break-words">${label}</h4>
                                <div class="w-4 h-4 ${colors.accent} rounded-full animate-pulse flex-shrink-0"></div>
                            </div>
                            <div class="h-1.5 w-24 ${colors.accent} rounded-full"></div>
                        </div>
                        <div class="bg-black/30 backdrop-blur-md rounded-3xl p-12 mb-6 border-2 border-white/20">
                            <p class="text-base font-bold ${colors.label} uppercase tracking-widest mb-6 text-center">Range Display</p>
                            <div class="flex items-center justify-center gap-4">
                                <span id="range-start-${queueNum}" class="text-7xl font-black ${colors.current} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">${sq.current_number}</span>
                                <span class="text-6xl font-black ${colors.label}">-</span>
                                <span id="range-end-${queueNum}" class="text-7xl font-black ${colors.current} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">${sq.next_number}</span>
                            </div>
                        </div>
                    </div>`;
                } else {
                    // Sequential display
                    queueHtml+=`
                    <div class="${colors.bg} rounded-3xl shadow-2xl border-4 ${colors.border} p-10 w-full sm:w-[48%] lg:w-[31%] max-w-md">
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-3xl font-bold ${colors.label} break-words">${label}</h4>
                                <div class="w-4 h-4 ${colors.accent} rounded-full animate-pulse flex-shrink-0"></div>
                            </div>
                            <div class="h-1.5 w-24 ${colors.accent} rounded-full"></div>
                        </div>
                        <div class="bg-black/30 backdrop-blur-md rounded-3xl p-12 mb-6 border-2 border-white/20">
                            <p class="text-base font-bold ${colors.label} uppercase tracking-widest mb-6 text-center">Current Number</p>
                            <span id="current-number-${queueNum}" class="text-9xl font-black ${colors.current} block text-center leading-none break-all" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">${sq.current_number}</span>
                        </div>
                        <div class="bg-black/20 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                            <p class="text-sm font-semibold ${colors.label} uppercase tracking-wider mb-3 text-center">Next Number</p>
                            <span id="next-number-${queueNum}" class="text-6xl font-black ${colors.next} block text-center leading-none break-all" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">${sq.next_number}</span>
                        </div>
                    </div>`;
                }
            });

            const queueApiUrl = '{{ route('app.queues.fetchApi',['clinic'=>$clinic->id]) }}';
            secondScreen.document.open();
            secondScreen.document.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <title>${clinicQueueName} - Queue Display</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <script src="https://cdn.tailwindcss.com"><\/script>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
                        body { 
                            font-family: 'Inter', sans-serif;
                            overflow: hidden;
                            background: linear-gradient(to bottom right, #0f172a, #1e3a8a, #0f172a);
                        }
                        @keyframes pulseGlow {
                            0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); }
                            50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.8); }
                        }
                        @keyframes numberUpdate {
                            0% { transform: scale(1); }
                            50% { transform: scale(1.1); }
                            100% { transform: scale(1); }
                        }
                        .queue-active {
                            animation: pulseGlow 2s ease-in-out infinite;
                        }
                        .number-update {
                            animation: numberUpdate 0.5s ease-out;
                        }
                    </style>
                </head>
                <body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 min-h-screen p-8 flex flex-col items-center justify-center">
                    <div class="w-full max-w-[1920px] mx-auto flex flex-col items-center">
                        <h1 class="text-5xl font-bold text-white text-center mb-12">${clinicQueueName}</h1>
                        <div class="flex flex-wrap justify-center items-start gap-3 sm:gap-6 lg:gap-8 xl:gap-10 w-full">
                            ${queueHtml}
                        </div>
                    </div>
                    <script src="{{ asset('js/screenHeartbeat.js') }}"><\/script>
                    <script>
                        // Update queue numbers via AJAX (no page reload)
                        const queueApiEndpoint = '${queueApiUrl}';
                        const isRangeType = @json($queue->isRangeType() ?? false);
                        function updateQueueNumbers() {
                            fetch(queueApiEndpoint)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.subQueues) {
                                        data.subQueues.forEach((sq) => {
                                            const queueNum = sq.queue_number;
                                            
                                            if(isRangeType) {
                                                // Range-based update
                                                const rangeStartEl = document.getElementById(\`range-start-\${queueNum}\`);
                                                const rangeEndEl = document.getElementById(\`range-end-\${queueNum}\`);
                                                
                                                if (rangeStartEl) {
                                                    const oldStart = parseInt(rangeStartEl.textContent);
                                                    if (sq.current_number !== oldStart) {
                                                        rangeStartEl.classList.add('number-update');
                                                        rangeStartEl.textContent = sq.current_number;
                                                        setTimeout(() => rangeStartEl.classList.remove('number-update'), 500);
                                                    } else {
                                                        rangeStartEl.textContent = sq.current_number;
                                                    }
                                                }
                                                
                                                if (rangeEndEl) {
                                                    const oldEnd = parseInt(rangeEndEl.textContent);
                                                    if (sq.next_number !== oldEnd) {
                                                        rangeEndEl.classList.add('number-update');
                                                        rangeEndEl.textContent = sq.next_number;
                                                        setTimeout(() => rangeEndEl.classList.remove('number-update'), 500);
                                                    } else {
                                                        rangeEndEl.textContent = sq.next_number;
                                                    }
                                                }
                                            } else {
                                                // Sequential update
                                                const currentEl = document.getElementById(\`current-number-\${queueNum}\`);
                                                const nextEl = document.getElementById(\`next-number-\${queueNum}\`);
                                                
                                                if (currentEl) {
                                                    const oldValue = parseInt(currentEl.textContent);
                                                    if (sq.current_number !== oldValue) {
                                                        currentEl.classList.add('number-update');
                                                        currentEl.textContent = sq.current_number;
                                                        setTimeout(() => currentEl.classList.remove('number-update'), 500);
                                                    } else {
                                                        currentEl.textContent = sq.current_number;
                                                    }
                                                }
                                                
                                                if (nextEl) {
                                                    nextEl.textContent = sq.next_number;
                                                }
                                            }
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.warn('Queue update error:', err);
                                });
                        }
                        
                        // Auto-update queue numbers every 3 seconds (AJAX, no page reload)
                        setInterval(updateQueueNumbers, 3000);
                        updateQueueNumbers(); // Initial load
                        
                        // Initialize heartbeat - token will be set after registration
                        // Note: For queue screens, token is registered before HTML write
                        // We'll try to get it from parent window or use sessionStorage
                        document.addEventListener('DOMContentLoaded', function() {
                            // Try to get token from sessionStorage (set by parent window)
                            const screenToken = sessionStorage.getItem('queue_screen_token');
                            if (screenToken && typeof screenHeartbeat !== 'undefined') {
                                screenHeartbeat.init(screenToken);
                            }
                            
                            // Initial queue update
                            updateQueueNumbers();
                        });
                    <\/script>
                </body>
                </html>
            `);
            secondScreen.document.close();
        });
    }

    // Live queue update with enhanced animation (preserved functionality + enhanced)
    let currentQueueNumbers = {};
    let currentQueueRanges = {}; // Store range data for comparison
    function fetchQueueLive(){
        const isRangeType = window.queueConfig?.isRangeType || false;
        fetch("{{ route('app.queues.fetchApi', ['clinic' => $clinic->id]) }}")
        .then(res=>res.json())
        .then(data=>{
            data.subQueues.forEach((sq,index)=>{
                const i=sq.queue_number || (index+1);
                
                if(isRangeType) {
                    // Range-based queue update
                    const rangeStartEl = document.getElementById(`range-start-${i}`);
                    const rangeEndEl = document.getElementById(`range-end-${i}`);
                    const rangeStartInput = document.getElementById(`range-start-input-${i}`);
                    const rangeEndInput = document.getElementById(`range-end-input-${i}`);
                    
                    const rangeKey = `${i}-${sq.current_number}-${sq.next_number}`;
                    const oldRangeKey = currentQueueRanges[i];
                    
                    // Check if range changed
                    if(rangeKey !== oldRangeKey) {
                        if(rangeStartEl) {
                            rangeStartEl.classList.add('number-update');
                            rangeStartEl.textContent = sq.current_number;
                            setTimeout(() => rangeStartEl.classList.remove('number-update'), 500);
                        }
                        if(rangeEndEl) {
                            rangeEndEl.classList.add('number-update');
                            rangeEndEl.textContent = sq.next_number;
                            setTimeout(() => rangeEndEl.classList.remove('number-update'), 500);
                        }
                        if(rangeStartInput) rangeStartInput.value = sq.current_number;
                        if(rangeEndInput) rangeEndInput.value = sq.next_number;
                        
                        currentQueueRanges[i] = rangeKey;
                    } else {
                        // Update without animation if values haven't changed
                        if(rangeStartEl) rangeStartEl.textContent = sq.current_number;
                        if(rangeEndEl) rangeEndEl.textContent = sq.next_number;
                        if(rangeStartInput) rangeStartInput.value = sq.current_number;
                        if(rangeEndInput) rangeEndInput.value = sq.next_number;
                    }
                } else {
                    // Sequential queue update
                    const curEl=document.getElementById(`current-number-${i}`);
                    const nextEl=document.getElementById(`next-number-${i}`);
                    
                    // Check if number changed and add animation
                    if(sq.current_number!==currentQueueNumbers[i]) {
                        if (typeof speakNumber === 'function') {
                            speakNumber(sq.current_number);
                        }
                        
                        // Add enhanced animation class
                        if(curEl) {
                            curEl.classList.add('number-update');
                            curEl.textContent = sq.current_number;
                            setTimeout(() => curEl.classList.remove('number-update'), 500);
                        }
                    } else {
                        if(curEl) curEl.textContent = sq.current_number;
                    }
                    
                    if(nextEl) nextEl.textContent = sq.next_number;
                    currentQueueNumbers[i]=sq.current_number;
                }
            });
            updateSecondScreen();
        }).catch(err=>console.error(err));
    }
    
    // Start polling
    setInterval(fetchQueueLive,3000);
    fetchQueueLive();
</script>
@endpush
