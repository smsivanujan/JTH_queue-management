@extends('layouts.platform')

@section('title', 'Alert Details - SmartQueue')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-cyan-50">
    <!-- Header -->
    <div class="bg-white/95 backdrop-blur-md border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Alert Details</h1>
                        <p class="text-sm text-gray-500">View and manage alert information</p>
                    </div>
                </div>
                <a href="{{ route('platform.alerts.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                    ← Back to Alerts
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8">
            <!-- Alert Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-6 border-b border-gray-200">
                <div class="mb-4 sm:mb-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $alert->title }}</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $severityColors = [
                                'low' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'medium' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'high' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'critical' => 'bg-red-100 text-red-800 border-red-200',
                            ];
                            $color = $severityColors[$alert->severity] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $color }} border">
                            {{ ucfirst($alert->severity) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                            {{ ucfirst(str_replace('_', ' ', $alert->type)) }}
                        </span>
                        @if($alert->isResolved())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                Resolved
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                Active
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($alert->isResolved())
                        <form action="{{ route('platform.alerts.unresolve', $alert) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors text-sm">
                                Reopen Alert
                            </button>
                        </form>
                    @else
                        <form action="{{ route('platform.alerts.resolve', $alert) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-sm">
                                Mark as Resolved
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Alert Message -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Message</h3>
                <p class="text-gray-700 leading-relaxed">{{ $alert->message }}</p>
            </div>

            <!-- Alert Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Tenant</h3>
                    <p class="text-gray-900">
                        @if($alert->tenant)
                            <a href="{{ route('platform.tenants.index') }}" class="text-blue-600 hover:text-blue-800 underline">
                                {{ $alert->tenant->name }}
                            </a>
                            <br>
                            <span class="text-sm text-gray-600">{{ $alert->tenant->email }}</span>
                        @else
                            <span class="text-gray-600">System-wide alert</span>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Last Triggered</h3>
                    <p class="text-gray-900">
                        {{ $alert->last_triggered_at ? $alert->last_triggered_at->format('F j, Y g:i A') : 'Never' }}
                        @if($alert->last_triggered_at)
                            <br>
                            <span class="text-sm text-gray-600">{{ $alert->last_triggered_at->diffForHumans() }}</span>
                        @endif
                    </p>
                </div>
                @if($alert->resolved_at)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Resolved At</h3>
                        <p class="text-gray-900">
                            {{ $alert->resolved_at->format('F j, Y g:i A') }}
                            <br>
                            <span class="text-sm text-gray-600">{{ $alert->resolved_at->diffForHumans() }}</span>
                        </p>
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Created</h3>
                    <p class="text-gray-900">
                        {{ $alert->created_at->format('F j, Y g:i A') }}
                        <br>
                        <span class="text-sm text-gray-600">{{ $alert->created_at->diffForHumans() }}</span>
                    </p>
                </div>
            </div>

            <!-- Metadata -->
            @if($alert->metadata && count($alert->metadata) > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-3">Additional Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($alert->metadata as $key => $value)
                                <div>
                                    <dt class="text-xs font-semibold text-gray-600 uppercase mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if(is_array($value))
                                            {{ json_encode($value) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

