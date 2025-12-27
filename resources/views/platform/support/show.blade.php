@extends('layouts.platform')

@section('title', 'Support Ticket #' . $supportTicket->id . ' - SmartQueue')

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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Support Ticket #{{ $supportTicket->id }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $supportTicket->subject }}</p>
                </div>
                <a href="{{ route('platform.support.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-sm">
                    ← Back to Tickets
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

        <div class="space-y-6">
            <!-- Ticket Details Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sm:p-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-6 border-b border-gray-200">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $supportTicket->subject }}</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            @php
                                $statusColors = [
                                    'open' => 'bg-red-100 text-red-800 border-red-200',
                                    'replied' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'closed' => 'bg-green-100 text-green-800 border-green-200',
                                ];
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'normal' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'high' => 'bg-orange-100 text-orange-800 border-orange-200',
                                    'urgent' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $statusColor = $statusColors[$supportTicket->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                $priorityColor = $priorityColors[$supportTicket->priority] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }} border">
                                {{ ucfirst($supportTicket->status) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $priorityColor }} border">
                                {{ ucfirst($supportTicket->priority) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                {{ ucfirst(str_replace('_', ' ', $supportTicket->category)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($supportTicket->isClosed())
                            <form action="{{ route('platform.support.reopen', $supportTicket) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm">
                                    Reopen Ticket
                                </button>
                            </form>
                        @elseif($supportTicket->isReplied())
                            <form action="{{ route('platform.support.markClosed', $supportTicket) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-sm">
                                    Mark as Closed
                                </button>
                            </form>
                        @else
                            <form action="{{ route('platform.support.markReplied', $supportTicket) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors text-sm">
                                    Mark as Replied
                                </button>
                            </form>
                            <form action="{{ route('platform.support.markClosed', $supportTicket) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-sm">
                                    Mark as Closed
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Ticket Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Tenant</h3>
                        <p class="text-gray-900">{{ $supportTicket->tenant->name }}</p>
                        <p class="text-sm text-gray-600">{{ $supportTicket->tenant->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Submitted By</h3>
                        <p class="text-gray-900">{{ $supportTicket->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $supportTicket->user->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Created</h3>
                        <p class="text-gray-900">
                            {{ $supportTicket->created_at->format('F j, Y g:i A') }}
                            <br>
                            <span class="text-sm text-gray-600">{{ $supportTicket->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                    @if($supportTicket->replied_at)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Replied At</h3>
                            <p class="text-gray-900">
                                {{ $supportTicket->replied_at->format('F j, Y g:i A') }}
                                <br>
                                <span class="text-sm text-gray-600">{{ $supportTicket->replied_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @endif
                    @if($supportTicket->closed_at)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Closed At</h3>
                            <p class="text-gray-900">
                                {{ $supportTicket->closed_at->format('F j, Y g:i A') }}
                                <br>
                                <span class="text-sm text-gray-600">{{ $supportTicket->closed_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Message -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Message</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $supportTicket->message }}</p>
                    </div>
                </div>

                <!-- Admin Notes -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 uppercase mb-2">Admin Notes</h3>
                    <form method="POST" action="{{ route($supportTicket->isReplied() ? 'platform.support.markClosed' : 'platform.support.markReplied', $supportTicket) }}" class="space-y-3">
                        @csrf
                        <textarea 
                            name="admin_notes" 
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add internal notes or response details..."
                        >{{ old('admin_notes', $supportTicket->admin_notes) }}</textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm">
                                {{ $supportTicket->isReplied() ? 'Update Notes & Close' : 'Update Notes & Mark Replied' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

