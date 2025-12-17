<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <svg class="me-2" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2563eb;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="fw-bold h5" style="background: linear-gradient(to right, #2563eb, #14b8a6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">SmartQueue</span>
            <span class="ms-2 small text-muted d-none d-md-inline">Queue Management System</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                @auth
                    @if(auth()->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium {{ request()->routeIs('platform.*') ? 'text-primary' : '' }}" href="{{ route('platform.dashboard') }}">Platform</a>
                        </li>
                    @endif
                    @if(app()->bound('tenant'))
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-medium {{ request()->routeIs('app.dashboard') ? 'text-primary' : '' }}" href="{{ route('app.dashboard') }}">Dashboard</a>
                        </li>
                        @admin
                            <li class="nav-item">
                                <a class="nav-link text-dark fw-medium {{ request()->routeIs('app.staff.*') ? 'text-primary' : '' }}" href="{{ route('app.staff.index') }}">Staff</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fw-medium {{ request()->routeIs('app.subscription.*') ? 'text-primary' : '' }}" href="{{ route('app.subscription.index') }}">
                                    <span class="d-none d-md-inline">Billing & Subscription</span>
                                    <span class="d-md-none">Billing</span>
                                </a>
                            </li>
                        @endadmin
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ route('pricing') }}">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark fw-medium" href="{{ route('tenant.register') }}">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
</div>
