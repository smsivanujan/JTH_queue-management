<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('app.dashboard') }}">
            <svg class="me-2" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2563eb;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="fw-bold" style="background: linear-gradient(to right, #2563eb, #14b8a6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">SmartQueue</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tenantNavbar"
            aria-controls="tenantNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="tenantNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('app.dashboard') ? 'active text-primary fw-bold' : 'text-dark' }}" 
                       href="{{ route('app.dashboard') }}">Dashboard</a>
                </li>
                @admin
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('app.staff.*') ? 'active text-primary fw-bold' : 'text-dark' }}" 
                           href="{{ route('app.staff.index') }}">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('app.clinic.*') ? 'active text-primary fw-bold' : 'text-dark' }}" 
                           href="{{ route('app.clinic.index') }}">Clinics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('app.subscription.*') || request()->routeIs('app.plans.*') ? 'active text-primary fw-bold' : 'text-dark' }}" 
                           href="{{ route('app.subscription.index') }}">
                            <span class="d-none d-md-inline">Billing & Subscription</span>
                            <span class="d-md-none">Billing</span>
                        </a>
                    </li>
                @endadmin
            </ul>
            <ul class="navbar-nav">
                @if(auth()->check() && auth()->user()->isSuperAdmin() && app()->bound('tenant'))
                    <li class="nav-item">
                        <form action="{{ route('tenant.exit') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="fas fa-sign-out-alt me-1"></i>Exit Tenant
                            </button>
                        </form>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
            </ul>
        </div>
    </div>
</nav>

