@extends('layouts.landing')

@section('title', 'Login - SmartQueue')

@push('styles')
<style>
    .login-section {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .login-container {
        max-width: 420px;
        width: 100%;
        background: #ffffff;
        padding: 48px 40px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .login-container h1 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1f2937;
        text-align: center;
    }

    .login-container .subtitle {
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

    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.2s;
        color: #1f2937;
    }

    .form-group input[type="email"]:focus,
    .form-group input[type="password"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .form-check input[type="checkbox"] {
        margin-right: 8px;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .form-check label {
        font-size: 14px;
        color: #4b5563;
        font-weight: 400;
        margin: 0;
        cursor: pointer;
    }

    .btn-login {
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
    }

    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(59,130,246,0.3);
    }

    .register-link {
        margin-top: 24px;
        text-align: center;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .register-link p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    .register-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }

    .register-link a:hover {
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
</style>
@endpush

@section('content')
<div class="login-section">
    <div class="login-container">
        <h1>Welcome Back</h1>
        <p class="subtitle">Sign in to your account to continue</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="{{ route('tenant.register') }}">Create one now</a></p>
        </div>
    </div>
</div>
@endsection

