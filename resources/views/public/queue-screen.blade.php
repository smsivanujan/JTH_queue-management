<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $clinic->name }} - Queue Display</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('js/tvFullscreen.js') }}"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.4); }
            50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.8); }
        }
        .queue-active {
            animation: pulseGlow 2s ease-in-out infinite;
        }
        @keyframes numberUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .number-update {
            animation: numberUpdate 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 min-h-screen p-4 sm:p-6 lg:p-8 xl:p-12 flex items-center justify-center">
    <div class="max-w-[1920px] mx-auto w-full flex flex-col items-center">
        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl 2xl:text-8xl font-bold text-white text-center mb-6 sm:mb-8 lg:mb-12 break-words px-4">{{ $clinic->name }}</h1>
        <div class="flex flex-wrap justify-center items-start gap-4 sm:gap-6 lg:gap-8 xl:gap-10 w-full">
            @php
                $colorSchemes = [
                    ['bg' => 'bg-gradient-to-br from-blue-600 to-blue-800', 'border' => 'border-blue-400', 'current' => 'text-blue-100', 'next' => 'text-blue-200', 'label' => 'text-blue-100', 'accent' => 'bg-blue-500'],
                    ['bg' => 'bg-gradient-to-br from-purple-600 to-purple-800', 'border' => 'border-purple-400', 'current' => 'text-purple-100', 'next' => 'text-purple-200', 'label' => 'text-purple-100', 'accent' => 'bg-purple-500'],
                    ['bg' => 'bg-gradient-to-br from-teal-600 to-teal-800', 'border' => 'border-teal-400', 'current' => 'text-teal-100', 'next' => 'text-teal-200', 'label' => 'text-teal-100', 'accent' => 'bg-teal-500'],
                    ['bg' => 'bg-gradient-to-br from-amber-600 to-amber-800', 'border' => 'border-amber-400', 'current' => 'text-amber-100', 'next' => 'text-amber-200', 'label' => 'text-amber-100', 'accent' => 'bg-amber-500'],
                    ['bg' => 'bg-gradient-to-br from-pink-600 to-pink-800', 'border' => 'border-pink-400', 'current' => 'text-pink-100', 'next' => 'text-pink-200', 'label' => 'text-pink-100', 'accent' => 'bg-pink-500'],
                ];
                $labels = ['Urine Test', 'Full Blood Count', 'ESR'];
            @endphp
            @foreach($subQueues as $index => $subQueue)
                @php
                    $i = $subQueue->queue_number ?? ($index + 1);
                    $colors = $colorSchemes[($i-1) % count($colorSchemes)];
                    $label = ($queue && $queue->display == 3 && $clinic->id == 2) ? ($labels[$i-1] ?? "Queue #{$i}") : "Queue #{$i}";
                @endphp
                <div class="{{ $colors['bg'] }} rounded-2xl sm:rounded-3xl shadow-2xl border-2 sm:border-4 {{ $colors['border'] }} p-6 sm:p-8 lg:p-10 xl:p-12 queue-active w-full sm:w-[48%] lg:w-[31%] max-w-md">
                    <div class="mb-4 sm:mb-6 lg:mb-8">
                        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
                            <h4 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold {{ $colors['label'] }} break-words">{{ $label }}</h4>
                            <div class="w-3 h-3 sm:w-4 sm:h-4 {{ $colors['accent'] }} rounded-full animate-pulse flex-shrink-0 ml-2"></div>
                        </div>
                        <div class="h-1 sm:h-1.5 w-16 sm:w-24 lg:w-32 {{ $colors['accent'] }} rounded-full"></div>
                    </div>
                    @if($queue && $queue->isRangeType())
                        <!-- Range-based Display -->
                        <div class="bg-black/30 backdrop-blur-md rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-10 xl:p-12 mb-4 sm:mb-6 border-2 border-white/20">
                            <p class="text-xs sm:text-sm md:text-base lg:text-lg font-bold {{ $colors['label'] }} uppercase tracking-widest mb-3 sm:mb-4 lg:mb-6 text-center">Range Display</p>
                            <div class="flex items-center justify-center gap-3 sm:gap-4 lg:gap-6">
                                <span id="range-start-{{ $i }}" class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl 2xl:text-9xl font-black {{ $colors['current'] }} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">{{ $subQueue->current_number }}</span>
                                <span class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black {{ $colors['label'] }}">-</span>
                                <span id="range-end-{{ $i }}" class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl 2xl:text-9xl font-black {{ $colors['current'] }} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">{{ $subQueue->next_number }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Sequential Display -->
                        <div class="bg-black/30 backdrop-blur-md rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-10 xl:p-12 mb-4 sm:mb-6 border-2 border-white/20">
                            <p class="text-xs sm:text-sm md:text-base lg:text-lg font-bold {{ $colors['label'] }} uppercase tracking-widest mb-3 sm:mb-4 lg:mb-6 text-center">Current Number</p>
                            <span id="current-number-{{ $i }}" class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl 2xl:text-[12rem] font-black {{ $colors['current'] }} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">{{ $subQueue->current_number }}</span>
                        </div>
                        <div class="bg-black/20 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 border border-white/10">
                            <p class="text-xs sm:text-sm font-semibold {{ $colors['label'] }} uppercase tracking-wider mb-2 sm:mb-3 text-center">Next Number</p>
                            <span id="next-number-{{ $i }}" class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black {{ $colors['next'] }} block text-center leading-none" style="text-shadow: 2px 4px 8px rgba(0, 0, 0, 0.1);">{{ $subQueue->next_number }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Laravel Echo and Pusher for WebSocket -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.min.js"></script>
    
    <!-- Heartbeat and Auto-Refresh Script -->
    <script src="{{ asset('js/screenHeartbeat.js') }}"></script>
    <script src="{{ asset('js/offlineFallback.js') }}"></script>
    <script src="{{ asset('js/websocketClient.js') }}"></script>
    <script>
        const screenToken = @json($screenToken);
        const clinicId = @json($clinic->id);
        const tenantId = @json($tenantId ?? null);
        
        // Reverb configuration
        const reverbConfig = {
            appKey: @json(config('broadcasting.connections.reverb.key')),
            host: @json(config('broadcasting.connections.reverb.options.host')),
            port: @json(config('broadcasting.connections.reverb.options.port', 443)),
            scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'https')),
            screenToken: screenToken
        };
        
        // Initialize offline fallback
        let offlineFallback;
        if (typeof OfflineFallback !== 'undefined') {
            offlineFallback = OfflineFallback;
            offlineFallback.init('queue_screen');
        }
        
        // Initialize heartbeat
        if (screenToken && typeof screenHeartbeat !== 'undefined') {
            document.addEventListener('DOMContentLoaded', function() {
                screenHeartbeat.init(screenToken);
            });
        }

        // Queue type from server
        const isRangeType = @json($queue && $queue->isRangeType() ? true : false);
        
        // Apply cached data to UI
        window.applyCachedData = function(data) {
            if (!data || !data.subQueues) return;
            
            data.subQueues.forEach((sq) => {
                const queueNumber = sq.queue_number;
                
                if(isRangeType) {
                    // Range-based update
                    const rangeStartEl = document.getElementById(`range-start-${queueNumber}`);
                    const rangeEndEl = document.getElementById(`range-end-${queueNumber}`);
                    
                    if (rangeStartEl) {
                        rangeStartEl.textContent = sq.current_number;
                    }
                    
                    if (rangeEndEl) {
                        rangeEndEl.textContent = sq.next_number;
                    }
                } else {
                    // Sequential update
                    const curEl = document.getElementById(`current-number-${queueNumber}`);
                    const nextEl = document.getElementById(`next-number-${queueNumber}`);
                    
                    if (curEl) {
                        curEl.textContent = sq.current_number;
                    }
                    
                    if (nextEl) {
                        nextEl.textContent = sq.next_number;
                    }
                }
            });
        };

        // WebSocket connection state
        let websocketActive = false;
        let pollingInterval = null;
        const POLLING_INTERVAL_MS = 3000; // 3 seconds
        
        // Update queue UI
        function updateQueueUI(data) {
            if (!data || !data.subQueues) {
                console.warn('updateQueueUI: No data or subQueues', data);
                return;
            }
            
            // Cache successful response
            if (offlineFallback) {
                offlineFallback.cacheData(data);
            }
            
            // Update UI
            data.subQueues.forEach((sq) => {
                const queueNumber = sq.queue_number;
                
                if(isRangeType) {
                    // Range-based update
                    const rangeStartElId = `range-start-${queueNumber}`;
                    const rangeEndElId = `range-end-${queueNumber}`;
                    const rangeStartEl = document.getElementById(rangeStartElId);
                    const rangeEndEl = document.getElementById(rangeEndElId);
                    
                    console.log(`Updating range queue ${queueNumber}: start=${sq.current_number}, end=${sq.next_number}`, {
                        rangeStartElId,
                        rangeEndElId,
                        rangeStartElFound: !!rangeStartEl,
                        rangeEndElFound: !!rangeEndEl
                    });
                    
                    if (rangeStartEl) {
                        const oldStart = parseInt(rangeStartEl.textContent.trim()) || 0;
                        const newStart = parseInt(sq.current_number) || 0;
                        
                        rangeStartEl.textContent = sq.current_number;
                        
                        if (oldStart !== newStart) {
                            rangeStartEl.classList.add('number-update');
                            setTimeout(() => rangeStartEl.classList.remove('number-update'), 500);
                            console.log(`Queue ${queueNumber} range start changed: ${oldStart} -> ${newStart}`);
                        }
                    } else {
                        console.warn(`Element not found: ${rangeStartElId}`);
                    }
                    
                    if (rangeEndEl) {
                        const oldEnd = parseInt(rangeEndEl.textContent.trim()) || 0;
                        const newEnd = parseInt(sq.next_number) || 0;
                        
                        rangeEndEl.textContent = sq.next_number;
                        
                        if (oldEnd !== newEnd) {
                            rangeEndEl.classList.add('number-update');
                            setTimeout(() => rangeEndEl.classList.remove('number-update'), 500);
                            console.log(`Queue ${queueNumber} range end changed: ${oldEnd} -> ${newEnd}`);
                        }
                    } else {
                        console.warn(`Element not found: ${rangeEndElId}`);
                    }
                } else {
                    // Sequential update
                    const curElId = `current-number-${queueNumber}`;
                    const nextElId = `next-number-${queueNumber}`;
                    const curEl = document.getElementById(curElId);
                    const nextEl = document.getElementById(nextElId);
                    
                    console.log(`Updating queue ${queueNumber}: current=${sq.current_number}, next=${sq.next_number}`, {
                        curElId,
                        nextElId,
                        curElFound: !!curEl,
                        nextElFound: !!nextEl
                    });
                    
                    if (curEl) {
                        const oldValue = parseInt(curEl.textContent.trim()) || 0;
                        const newValue = parseInt(sq.current_number) || 0;
                        
                        // Always update the text content
                        curEl.textContent = sq.current_number;
                        
                        // Add animation if number changed
                        if (oldValue !== newValue) {
                            curEl.classList.add('number-update');
                            setTimeout(() => curEl.classList.remove('number-update'), 500);
                            console.log(`Queue ${queueNumber} current number changed: ${oldValue} -> ${newValue}`);
                        }
                    } else {
                        console.warn(`Element not found: ${curElId}`);
                    }
                    
                    if (nextEl) {
                        nextEl.textContent = sq.next_number;
                    } else {
                        console.warn(`Element not found: ${nextElId}`);
                    }
                }
            });
        }
        
        // Fetch queue data via API (polling fallback)
        function fetchQueueLive() {
            // Generate signed URL for API endpoint (required by signed middleware)
            const url = `{{ URL::signedRoute('public.queue.api', ['screen_token' => $screenToken], now()->addHours(24)) }}`;
            const fetchMethod = offlineFallback ? offlineFallback.fetchWithOffline.bind(offlineFallback) : fetch;
            
            console.log('Fetching queue data from:', url);
            
            fetchMethod(url)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Queue data received:', data);
                    updateQueueUI(data);
                })
                .catch(err => {
                    // Error handled by offline fallback
                    console.error('Queue fetch error:', err);
                });
        }

        // Retry fetch function (called by offline fallback when connection restores)
        window.retryFetch = fetchQueueLive;
        
        // Start normal polling (3 seconds) - used as fallback when WebSocket is not active
        function startPolling() {
            stopPolling(); // Clear any existing interval
            
            // Don't start polling if WebSocket is active
            if (websocketActive) {
                return;
            }
            
            // Don't start aggressive polling if offline
            if (offlineFallback && offlineFallback.isOffline) {
                // Offline fallback handles graceful retry (15 seconds)
                return;
            }
            
            // Normal polling every 3 seconds when online and WebSocket is not active
            pollingInterval = setInterval(fetchQueueLive, POLLING_INTERVAL_MS);
            fetchQueueLive(); // Initial fetch
        }
        
        // Stop polling
        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }
        
        // Handle offline state changes (adjust polling)
        window.onOfflineStateChange = function(isOffline) {
            if (isOffline) {
                // Stop aggressive polling, use graceful retry instead
                stopPolling();
            } else {
                // Resume normal polling when online
                startPolling();
            }
        };
        
        // Initialize WebSocket connection
        function initializeWebSocket() {
            if (!tenantId || !reverbConfig.appKey) {
                console.warn('WebSocket configuration missing. Using polling only.');
                websocketActive = false;
                return;
            }
            
            // Initialize Echo
            if (typeof Echo !== 'undefined') {
                try {
                    window.Echo = new Echo({
                        broadcaster: 'reverb',
                        key: reverbConfig.appKey,
                        wsHost: reverbConfig.host,
                        wsPort: reverbConfig.port,
                        wssPort: reverbConfig.port,
                        forceTLS: reverbConfig.scheme === 'https' || reverbConfig.scheme === 'wss',
                        enabledTransports: ['ws', 'wss'],
                        authEndpoint: '/broadcasting/auth',
                        auth: {
                            headers: screenToken ? {
                                'X-Screen-Token': screenToken
                            } : {}
                        }
                    });
                    
                    // Subscribe to queue update channel
                    const channelName = `tenant.${tenantId}.queue.${clinicId}`;
                    
                    // Set a timeout to ensure polling starts if WebSocket fails
                    const wsTimeout = setTimeout(() => {
                        if (!websocketActive) {
                            console.warn('WebSocket connection timeout. Falling back to polling.');
                            websocketActive = false;
                            startPolling();
                        }
                    }, 5000); // 5 second timeout
                    
                    window.Echo.private(channelName)
                        .listen('.queue.updated', (data) => {
                            console.log('Queue updated via WebSocket:', data);
                            updateQueueUI(data);
                        })
                        .error((error) => {
                            console.error('WebSocket subscription error:', error);
                            clearTimeout(wsTimeout);
                            websocketActive = false;
                            startPolling();
                        });
                    
                    // Handle connection events
                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        clearTimeout(wsTimeout);
                        websocketActive = true;
                        stopPolling(); // Disable polling when WebSocket is active
                        console.log('WebSocket connected - polling disabled');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('disconnected', () => {
                        clearTimeout(wsTimeout);
                        websocketActive = false;
                        startPolling(); // Re-enable polling when WebSocket disconnects
                        console.warn('WebSocket disconnected - polling re-enabled');
                    });
                    
                    window.Echo.connector.pusher.connection.bind('error', (error) => {
                        console.error('WebSocket error:', error);
                        clearTimeout(wsTimeout);
                        websocketActive = false;
                        startPolling(); // Fallback to polling on error
                    });
                    
                    window.Echo.connector.pusher.connection.bind('unavailable', () => {
                        console.warn('WebSocket unavailable. Using polling.');
                        clearTimeout(wsTimeout);
                        websocketActive = false;
                        startPolling();
                    });
                    
                    console.log(`Attempting to subscribe to WebSocket channel: ${channelName}`);
                } catch (error) {
                    console.error('Failed to initialize WebSocket:', error);
                    // Fallback to polling
                    websocketActive = false;
                    startPolling();
                }
            } else {
                console.warn('Laravel Echo not available. Using polling only.');
                websocketActive = false;
                startPolling();
            }
        }
        
        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            // Load cached data if available
            if (offlineFallback) {
                offlineFallback.loadCachedData();
            }
            
            // Always start polling first (most reliable for public screens)
            // WebSocket will disable polling if it successfully connects
            console.log('Starting polling for queue updates...');
            startPolling();
            
            // Try to initialize WebSocket (will disable polling if successful)
            initializeWebSocket();
        });
    </script>
</body>
</html>

