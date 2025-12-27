@extends('layouts.tenant')

@section('title', $service->name . ' Queue - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    /* Hide layout header and footer for service display */
    .header_section {
        display: none;
    }
    .footer_section {
        display: none;
    }
    
    /* Token styling - generic for any service */
    .token {
        padding: 1.5rem;
        border-radius: 1rem;
        border: 3px solid;
        min-width: 80px;
        min-height: 80px;
        font-size: 1.5rem;
        font-weight: 800;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s, box-shadow 0.2s;
        color: #1f2937;
    }
    
    /* Color-specific token styling - JavaScript sets backgroundColor inline */
    .token[style*="background-color: white"],
    .token[style*="background-color: rgb(255, 255, 255)"] {
        border-color: #6366f1;
        background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
        color: #1f2937;
    }
    
    .token[style*="background-color: red"],
    .token[style*="background-color: rgb(255, 0, 0)"] {
        border-color: #dc2626;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .token[style*="background-color: green"],
    .token[style*="background-color: rgb(0, 128, 0)"],
    .token[style*="background-color: rgb(0, 255, 0)"] {
        border-color: #16a34a;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
    }
    
    .token[style*="background-color: blue"],
    .token[style*="background-color: rgb(0, 0, 255)"] {
        border-color: #2563eb;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .token:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 640px) {
        .token {
            min-width: 60px;
            min-height: 60px;
            font-size: 1.2rem;
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex flex-col">
    <!-- Header -->
    <div class="bg-slate-800/90 backdrop-blur-md border-b border-slate-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-4 sm:py-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white">{{ $service->name }}</h1>
                    <p class="text-xs sm:text-sm lg:text-base text-slate-300 mt-1">Service Queue Management</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logoutForm" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg sm:rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg text-sm sm:text-base touch-manipulation">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 inline-block sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        EXIT
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            
            <!-- Control Panel Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl shadow-2xl border border-white/20 p-4 sm:p-6 lg:p-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Control Panel
                </h2>

                @if($service->isRangeType())
                    <!-- Range-based calling (start-end range) -->
                    <div class="mb-4 sm:mb-6 lg:mb-8">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
                            <div>
                                <label for="startValue" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                    Start Value
                                </label>
                                <input 
                                    type="number" 
                                    id="startValue" 
                                    placeholder="0" 
                                    min="0"
                                    class="w-full px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-lg sm:text-xl lg:text-2xl font-bold text-center border-2 border-gray-300 rounded-lg sm:rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 outline-none transition-all bg-gray-50 hover:bg-white touch-manipulation"
                                />
                            </div>
                            <div>
                                <label for="endValue" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                                    End Value
                                </label>
                                <input 
                                    type="number" 
                                    id="endValue" 
                                    placeholder="0" 
                                    min="0"
                                    class="w-full px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-lg sm:text-xl lg:text-2xl font-bold text-center border-2 border-gray-300 rounded-lg sm:rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 outline-none transition-all bg-gray-50 hover:bg-white touch-manipulation"
                                />
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Sequential calling (single number) -->
                    <div class="mb-4 sm:mb-6 lg:mb-8">
                        <label for="numberValue" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1 sm:mb-2">
                            Number
                        </label>
                        <input 
                            type="number" 
                            id="numberValue" 
                            placeholder="0" 
                            min="0"
                            class="w-full px-3 sm:px-4 lg:px-6 py-3 sm:py-4 text-lg sm:text-xl lg:text-2xl font-bold text-center border-2 border-gray-300 rounded-lg sm:rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 outline-none transition-all bg-gray-50 hover:bg-white touch-manipulation"
                        />
                    </div>
                @endif

                <!-- Label Selection (Service options) -->
                @if($labels->count() > 0)
                <div class="mb-4 sm:mb-6 lg:mb-8">
                    <label for="labelSelect" class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2 sm:mb-3">
                        Select {{ $service->name }} Option
                    </label>
                    <select 
                        id="labelSelect" 
                        class="w-full px-4 sm:px-6 py-3 sm:py-4 text-base sm:text-lg font-semibold border-2 border-gray-300 rounded-lg sm:rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 outline-none transition-all bg-white hover:bg-gray-50 cursor-pointer touch-manipulation"
                    >
                        <option value="">-- Select Option --</option>
                        @foreach($labels as $label)
                            <option value="{{ $label->id }}" data-label="{{ $label->label }}" data-color="{{ $label->color }}">{{ $label->label }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3 sm:space-y-4">
                    <button 
                        id="callBtn"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4 sm:py-5 rounded-xl sm:rounded-2xl font-bold text-lg sm:text-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 touch-manipulation"
                    >
                        CALL
                    </button>
                    <button 
                        id="openSecondScreen"
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-4 sm:py-5 rounded-xl sm:rounded-2xl font-bold text-lg sm:text-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 touch-manipulation"
                    >
                        Open Second Screen
                    </button>
                </div>
            </div>

            <!-- Display Panel Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl shadow-2xl border border-white/20 p-4 sm:p-6 lg:p-8">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Display Panel
                </h2>
                <div id="tokenDisplay" class="min-h-[400px] flex flex-wrap gap-4 sm:gap-6 items-start justify-start p-4 bg-gray-50 rounded-lg">
                    <!-- Tokens will be displayed here -->
                    <p class="text-gray-400 text-center w-full mt-8">Select options and click CALL to display tokens</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('public/js/service.js') }}"></script>
<script>
    // Service configuration
    window.serviceConfig = {
        serviceId: {{ $service->id }},
        serviceName: @json($service->name),
        serviceType: @json($service->type),
        labels: @json($labels->map(function($label) {
            return [
                'id' => $label->id,
                'label' => $label->label,
                'color' => $label->color,
                'translations' => $label->translations
            ];
        })),
        broadcastRoute: @json(route('app.service.broadcast', $service->id)),
        secondScreenRoute: @json(route('app.service.second-screen', $service->id))
    };
</script>
@endpush
@endsection

