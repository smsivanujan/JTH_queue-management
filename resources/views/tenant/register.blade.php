@extends('layouts.app')

@section('title', 'Register - SmartQueue')

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

    .register-container {
        max-width: 600px;
        width: 100%;
        background: #ffffff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        border: 1px solid #007bff;
    }

    h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    input:focus,
    textarea:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .btn-register {
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
        margin-top: 10px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    .login-link {
        margin-top: 20px;
        text-align: center;
    }

    .login-link a {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        margin-top: 30px;
        margin-bottom: 15px;
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }

    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="register-container">
    <h1>Register Organization</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
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
            <label for="name">Organization Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Organization Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address">{{ old('address') }}</textarea>
        </div>

        <div class="section-title">Admin Account</div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_name">Your Name *</label>
                <input type="text" id="user_name" name="user_name" value="{{ old('user_name') }}" required>
            </div>

            <div class="form-group">
                <label for="user_email">Your Email *</label>
                <input type="email" id="user_email" name="user_email" value="{{ old('user_email') }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-group">
            <label class="flex items-start" style="font-weight: normal; font-size: 14px;">
                <input type="checkbox" name="terms_accepted" required style="margin-top: 4px; margin-right: 8px;">
                <span>
                    I agree to the <a href="{{ route('legal.terms') }}" target="_blank" style="color: #007bff; text-decoration: underline;">Terms of Service</a> 
                    and <a href="{{ route('legal.privacy') }}" target="_blank" style="color: #007bff; text-decoration: underline;">Privacy Policy</a>
                </span>
            </label>
        </div>

        <button type="submit" class="btn-register">Register Organization</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
</div>
@endsection

