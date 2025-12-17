<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\PublicScreenController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('home');

// Pricing page (public)
Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

// Public signed routes for second screens (no auth required, signed URL validation only)
Route::middleware(['signed'])->group(function () {
    Route::get('/screen/pair/{screen_token}/{type}', [PublicScreenController::class, 'pair'])
        ->name('public.screen.pair');
    Route::get('/screen/queue/{screen_token}', [PublicScreenController::class, 'queue'])
        ->name('public.screen.queue');
    Route::get('/screen/queue/{screen_token}/api', [PublicScreenController::class, 'queueApi'])
        ->name('public.queue.api');
});

// Redirect authenticated users from landing to appropriate dashboard
Route::get('/welcome', function () {
    if (auth()->check()) {
        if (auth()->user()->isSuperAdmin() && !auth()->user()->current_tenant_id) {
            return redirect()->route('platform.dashboard');
        }
        return redirect()->route('app.dashboard');
    }
    return redirect()->route('login');
})->name('welcome');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [TenantController::class, 'showRegister'])->name('tenant.register');
    Route::post('/register', [TenantController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tenant selection (requires auth, no tenant)
Route::middleware('auth')->group(function () {
    Route::get('/tenant/select', [TenantController::class, 'select'])->name('tenant.select');
    Route::post('/tenant/switch/{tenant:slug}', [TenantController::class, 'switch'])->name('tenant.switch');
    
    // Super Admin: Exit tenant context (redirects to platform dashboard)
    Route::post('/tenant/exit', [TenantController::class, 'exit'])->name('tenant.exit');
});

// ========================================
// PLATFORM AREA (Super Admin Only)
// Prefix: /platform
// Middleware: auth + superAdmin
// ========================================
Route::middleware(['auth', 'superAdmin'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/dashboard', [PlatformController::class, 'dashboard'])->name('dashboard');
    
    // Tenant management (platform-level)
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [PlatformController::class, 'dashboard'])->name('index'); // Same as dashboard for now
    });
    
    // Platform-level plan management (future: create/edit plans)
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [PlatformController::class, 'dashboard'])->name('index'); // Placeholder
    });
    
    // Platform-level metrics (system-wide metrics)
    Route::prefix('metrics')->name('metrics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MetricsController::class, 'index'])->name('index');
    });
});

// ========================================
// TENANT APP AREA (Queue Management)
// Prefix: /app
// Middleware: auth + tenant + tenant.access + subscription
// ========================================
Route::middleware(['auth', 'tenant', 'tenant.access', 'subscription'])->prefix('app')->name('app.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Generic Service routes
    Route::post('/services/{service}/verify', [ServiceController::class, 'verifyPassword'])
        ->name('service.verify')
        ->middleware('throttle:5,1');
    
    Route::middleware('service.verify')->group(function () {
        Route::get('/services/{service}', [ServiceController::class, 'index'])->name('service.index');
        Route::get('/services/{service}/second-screen', [ServiceController::class, 'secondScreen'])->name('service.second-screen');
        Route::post('/services/{service}/broadcast', [ServiceController::class, 'broadcastUpdate'])->name('service.broadcast');
    });

    // Screen registration and heartbeat (for second screens)
    Route::middleware('screen.limit')->group(function () {
        Route::post('/screens/register', [ScreenController::class, 'register'])->name('screens.register');
        Route::post('/screens/heartbeat', [ScreenController::class, 'heartbeat'])->name('screens.heartbeat');
    });

    // Queue view routes (all authenticated users with password or role-based access)
    Route::middleware('queue.auth')->group(function () {
        Route::get('/queues/{clinic}', [QueueController::class, 'index'])->name('queues.index');
        
        // API routes (read-only access for viewing queue status)
        Route::get('/api/queue/{clinic}', [QueueController::class, 'getLiveQueue'])
            ->name('queues.fetchApi');
        
        // Queue management actions (only for users with queue management roles)
        Route::middleware('role:admin,reception,doctor')->group(function () {
            Route::post('/queues/{clinic}/next/{queueNumber}', [QueueController::class, 'next'])->name('queues.next');
            Route::post('/queues/{clinic}/previous/{queueNumber}', [QueueController::class, 'previous'])->name('queues.previous');
            Route::post('/queues/{clinic}/reset/{queueNumber}', [QueueController::class, 'reset'])->name('queues.reset');
        });
    });

    // Subscription routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        
        // Plan routes (for tenant self-activation)
        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans/{plan:slug}/activate', [PlanController::class, 'activate'])->name('plans.activate');
        Route::post('/plans/{plan:slug}/renew', [PlanController::class, 'renew'])->name('plans.renew');
        
        // Staff management routes
        Route::resource('staff', \App\Http\Controllers\StaffController::class)->names([
            'index' => 'staff.index',
            'create' => 'staff.create',
            'store' => 'staff.store',
            'show' => 'staff.show',
            'edit' => 'staff.edit',
            'update' => 'staff.update',
            'destroy' => 'staff.destroy',
        ]);
        Route::post('/staff/{staff}/reset-password', [\App\Http\Controllers\StaffController::class, 'resetPassword'])
            ->name('staff.reset-password');
        
        // Clinic management routes
        Route::resource('clinic', \App\Http\Controllers\ClinicController::class)->names([
            'index' => 'clinic.index',
            'create' => 'clinic.create',
            'store' => 'clinic.store',
            'show' => 'clinic.show',
            'edit' => 'clinic.edit',
            'update' => 'clinic.update',
            'destroy' => 'clinic.destroy',
        ]);
    });
});

// Subscription required page (accessible without subscription check)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/subscription/required', [SubscriptionController::class, 'required'])->name('subscription.required');
});

// Legacy route redirects (backward compatibility)
Route::middleware(['auth', 'tenant', 'tenant.access', 'subscription'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('app.dashboard');
    });
    
    Route::middleware('role:admin')->group(function () {
        Route::get('/subscription', function () {
            return redirect()->route('app.subscription.index');
        });
        Route::get('/plans', function () {
            return redirect()->route('app.plans.index');
        });
        Route::get('/metrics', function () {
            return redirect()->route('platform.metrics.index');
        });
    });
});
