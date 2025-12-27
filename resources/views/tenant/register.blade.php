@extends('layouts.landing')

@section('title', 'Register - SmartQueue')

@push('styles')
<style>
    .register-section {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .register-container {
        max-width: 700px;
        width: 100%;
        background: #ffffff;
        padding: 48px 40px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .register-container h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1f2937;
        text-align: center;
    }

    .register-container .subtitle {
        text-align: center;
        color: #6b7280;
        margin-bottom: 32px;
        font-size: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group input[type="password"],
    .form-group textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.2s;
        color: #1f2937;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-top: 32px;
        margin-bottom: 20px;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 12px;
    }

    .section-title:first-of-type {
        margin-top: 0;
    }

    .btn-register {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 6px rgba(59,130,246,0.2);
        margin-top: 8px;
    }

    .btn-register:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(59,130,246,0.3);
    }

    .login-link {
        margin-top: 24px;
        text-align: center;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .login-link p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .login-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    .terms-checkbox {
        display: flex;
        align-items: flex-start;
        margin-top: 8px;
    }

    .terms-checkbox input[type="checkbox"] {
        margin-top: 4px;
        margin-right: 12px;
        width: 18px;
        height: 18px;
        cursor: pointer;
        flex-shrink: 0;
    }

    .terms-checkbox label {
        font-weight: 400;
        font-size: 14px;
        color: #4b5563;
        line-height: 1.5;
        cursor: pointer;
    }

    .terms-checkbox a {
        color: #3b82f6;
        text-decoration: underline;
    }

    .terms-checkbox a:hover {
        color: #2563eb;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .register-container {
            padding: 32px 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="register-section">
    <div class="register-container">
        <h1>Create Your Account</h1>
        <p class="subtitle">Get started with SmartQueue in just a few steps</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.register') }}">
            @csrf

            <div class="section-title">Organization Information</div>

            <div class="form-group">
                <label for="name">Organization Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Organization Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" placeholder="Enter your organization's address">{{ old('address') }}</textarea>
            </div>

            <div class="section-title">Admin Account</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="user_name">Your Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="user_email">Your Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" id="user_email" name="user_email" value="{{ old('user_email') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password <span style="color: #ef4444;">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="form-group">
                <div class="terms-checkbox">
                    <input type="checkbox" id="terms_accepted" name="terms_accepted" required>
                    <label for="terms_accepted">
                        I agree to the <a href="{{ route('legal.terms') }}" target="_blank">Terms of Service</a> 
                        and <a href="{{ route('legal.privacy') }}" target="_blank">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
        </div>
    </div>
</div>
@endsection

