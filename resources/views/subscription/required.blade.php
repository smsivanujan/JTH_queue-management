@extends('layouts.app')

@section('title', 'Subscription Required - SmartQueue Hospital')

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

    .subscription-container {
        max-width: 600px;
        width: 100%;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        text-align: center;
        border: 1px solid #dc3545;
    }

    h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #dc3545;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }

    .subscription-info {
        text-align: left;
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .info-label {
        font-weight: 600;
        color: #333;
    }

    .info-value {
        color: #666;
        margin-top: 5px;
    }

    .btn-contact {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-top: 20px;
    }

    .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220,53,69,0.3);
    }
</style>
@endpush

@section('content')
<div class="subscription-container">
    <h1>Subscription Required</h1>

    <div class="alert alert-warning">
        <strong>Your subscription has expired or you're not on a trial period.</strong>
        <p style="margin-top: 10px; margin-bottom: 0;">Please renew your subscription or contact support to continue using the service.</p>
    </div>

    @if($tenant)
        <div class="subscription-info">
            <div class="info-item">
                <div class="info-label">Organization:</div>
                <div class="info-value">{{ $tenant->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $tenant->email }}</div>
            </div>
            @if($subscription)
                <div class="info-item">
                    <div class="info-label">Current Plan:</div>
                    <div class="info-value">{{ ucfirst($subscription->plan_name) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status:</div>
                    <div class="info-value">{{ ucfirst($subscription->status) }}</div>
                </div>
                @if($subscription->ends_at)
                    <div class="info-item">
                        <div class="info-label">Expires:</div>
                        <div class="info-value">{{ $subscription->ends_at->format('F d, Y') }}</div>
                    </div>
                @endif
            @elseif($isOnTrial)
                <div class="info-item">
                    <div class="info-label">Trial Status:</div>
                    <div class="info-value">Active</div>
                </div>
                @if($tenant->trial_ends_at)
                    <div class="info-item">
                        <div class="info-label">Trial Ends:</div>
                        <div class="info-value">{{ $tenant->trial_ends_at->format('F d, Y') }}</div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    <button class="btn-contact" onclick="alert('Please contact support at support@example.com to renew your subscription.')">
        Contact Support
    </button>
</div>
@endsection

