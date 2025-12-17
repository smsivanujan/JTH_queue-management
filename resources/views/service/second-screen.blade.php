@extends('layouts.tenant')

@section('title', $service->name . ' - Second Screen')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
    /* Hide layout header and footer */
    .header_section {
        display: none;
    }
    .footer_section {
        display: none;
    }
    
    /* Full screen token display */
    .token {
        padding: 2rem;
        border-radius: 1.5rem;
        border: 4px solid;
        min-width: 120px;
        min-height: 120px;
        font-size: 3rem;
        font-weight: 900;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
        color: #1f2937;
    }
    
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
    .token[style*="background-color: rgb(0, 128, 0)"] {
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-800 flex items-center justify-center p-8">
    <div class="w-full max-w-7xl">
        <div id="tokenDisplay" class="flex flex-wrap gap-8 items-center justify-center">
            <!-- Tokens will be displayed here -->
            <p class="text-white text-3xl font-bold">Waiting for tokens...</p>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('public/js/echo.js') }}"></script>
<script>
    // Service configuration
    const serviceConfig = {
        serviceId: {{ $service->id }},
        tenantId: @json(app()->bound('tenant') ? app('tenant')->id : null),
        screenToken: @json($screenToken ?? null)
    };
    
    // Listen for WebSocket updates
    if (typeof Echo !== 'undefined' && serviceConfig.tenantId && serviceConfig.serviceId) {
        Echo.private(`tenant.${serviceConfig.tenantId}.service.${serviceConfig.serviceId}`)
            .listen('.service.updated', (e) => {
                updateDisplay(e);
            });
    }
    
    function updateDisplay(data) {
        const tokenDisplay = document.getElementById('tokenDisplay');
        if (!tokenDisplay) return;
        
        tokenDisplay.innerHTML = '';
        
        if (data.tokens && data.tokens.length > 0) {
            data.tokens.forEach(token => {
                const div = document.createElement('div');
                div.className = 'token';
                div.textContent = token.number;
                div.style.backgroundColor = token.color;
                tokenDisplay.appendChild(div);
            });
        }
    }
    
    // Screen heartbeat (if available)
    if (typeof screenHeartbeat !== 'undefined' && serviceConfig.screenToken) {
        screenHeartbeat.start(serviceConfig.screenToken, 'service');
    }
</script>
@endpush
@endsection

