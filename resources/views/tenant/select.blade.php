@extends('layouts.app')

@section('title', 'Select Organization - SmartQueue Hospital')

@push('styles')
<style>
    html, body {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: linear-gradient(135deg, #373B44, #4286f4);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    main {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 15px;
    }

    .select-container {
        max-width: 600px;
        width: 100%;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        text-align: center;
        border: 1px solid #007bff;
    }

    h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
    }

    .tenant-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 30px;
    }

    .tenant-item {
        padding: 20px;
        border: 2px solid #ddd;
        border-radius: 12px;
        text-align: left;
        transition: all 0.3s;
        cursor: pointer;
    }

    .tenant-item:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.2);
    }

    .tenant-name {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .tenant-email {
        color: #666;
        font-size: 14px;
    }

    .tenant-role {
        display: inline-block;
        padding: 4px 12px;
        background: #007bff;
        color: #fff;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }

    .btn-switch {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #007bff, #00c6ff);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .btn-switch:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    .register-link {
        margin-top: 20px;
        text-align: center;
    }

    .register-link a {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .empty-state {
        padding: 40px;
        text-align: center;
        color: #666;
    }

    .empty-state p {
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="select-container">
    <h1>Select Organization</h1>

    @php
        $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
    @endphp

    @if($tenants->count() > 0)
        <div class="tenant-list">
            @foreach($tenants as $tenant)
                <form method="POST" action="{{ route('tenant.switch', $tenant->slug) }}" class="tenant-item">
                    @csrf
                    <div class="tenant-name">{{ $tenant->name }}</div>
                    <div class="tenant-email">{{ $tenant->email }}</div>
                    @if(!$isSuperAdmin && isset($tenant->pivot->role))
                        <span class="tenant-role">{{ ucfirst($tenant->pivot->role) }}</span>
                    @elseif($isSuperAdmin)
                        <span class="tenant-role" style="background: #28a745;">Platform Admin</span>
                    @endif
                    <button type="submit" class="btn-switch" style="margin-top: 15px;">
                        @if($isSuperAdmin)
                            Enter This Organization
                        @else
                            Select This Organization
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <p>You don't have access to any organizations yet.</p>
            <p>Register a new organization to get started.</p>
        </div>
    @endif

    <div class="register-link">
        @if($isSuperAdmin)
            <a href="{{ route('platform.dashboard') }}" class="inline-block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all duration-200">
                ← Back to Platform Dashboard
            </a>
        @else
            <p>Need to create a new organization? <a href="{{ route('tenant.register') }}">Register</a></p>
        @endif
    </div>
</div>
@endsection

