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
Route::get('/pricing', [PlanController::class, 'publicIndex'])->name('pricing');

// Legal pages (public)
Route::get('/terms', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/refunds', function () {
    return view('legal.refunds');
})->name('legal.refunds');

// Stripe webhook (no auth, signature verification only)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

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
        Route::get('/', [\App\Http\Controllers\MetricsController::class, 'platformIndex'])->name('index');
    });
    
    // Platform alerts
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Platform\AlertController::class, 'index'])->name('index');
        Route::get('/{alert}', [\App\Http\Controllers\Platform\AlertController::class, 'show'])->name('show');
        Route::post('/{alert}/resolve', [\App\Http\Controllers\Platform\AlertController::class, 'resolve'])->name('resolve');
        Route::post('/{alert}/unresolve', [\App\Http\Controllers\Platform\AlertController::class, 'unresolve'])->name('unresolve');
    });
    
    // Platform support ticket management
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Platform\SupportController::class, 'index'])->name('index');
        Route::get('/{supportTicket}', [\App\Http\Controllers\Platform\SupportController::class, 'show'])->name('show');
        Route::post('/{supportTicket}/mark-replied', [\App\Http\Controllers\Platform\SupportController::class, 'markReplied'])->name('mark-replied');
        Route::post('/{supportTicket}/mark-closed', [\App\Http\Controllers\Platform\SupportController::class, 'markClosed'])->name('mark-closed');
        Route::post('/{supportTicket}/reopen', [\App\Http\Controllers\Platform\SupportController::class, 'reopen'])->name('reopen');
    });
    
    // Platform reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Platform\ReportController::class, 'index'])->name('index');
        Route::get('/download', [\App\Http\Controllers\Platform\ReportController::class, 'download'])->name('download');
    });
});

// ========================================
// TENANT APP AREA (Queue Management)
// Prefix: /app
// Middleware: auth + tenant + tenant.access + subscription
// ========================================
Route::middleware(['auth', 'tenant', 'tenant.access', 'subscription'])->prefix('app')->name('app.')->group(function () {
    // Onboarding routes
    Route::get('/onboard', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/onboard/clinic', [\App\Http\Controllers\OnboardingController::class, 'clinic'])->name('onboarding.clinic');
    Route::post('/onboard/clinic', [\App\Http\Controllers\OnboardingController::class, 'storeClinic'])->name('onboarding.clinic.store');
    Route::get('/onboard/complete', [\App\Http\Controllers\OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::post('/onboard/skip', [\App\Http\Controllers\OnboardingController::class, 'skip'])->name('onboarding.skip');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


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
            Route::post('/queues/{clinic}/type', [QueueController::class, 'updateType'])->name('queues.type');
            Route::post('/queues/{clinic}/next/{queueNumber}', [QueueController::class, 'next'])->name('queues.next');
            Route::post('/queues/{clinic}/previous/{queueNumber}', [QueueController::class, 'previous'])->name('queues.previous');
            Route::post('/queues/{clinic}/reset/{queueNumber}', [QueueController::class, 'reset'])->name('queues.reset');
            Route::post('/queues/{clinic}/range/{queueNumber}', [QueueController::class, 'updateRange'])->name('queues.range');
        });
    });

    // Subscription routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        
        // Stripe payment routes
        Route::post('/stripe/checkout/{plan}', [\App\Http\Controllers\StripeController::class, 'checkout'])->name('stripe.checkout');
        Route::get('/stripe/success', [\App\Http\Controllers\StripeController::class, 'success'])->name('stripe.success');
        
        // Invoice routes
        Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
        
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
        
        // Support routes (all authenticated users)
        Route::get('/support', [\App\Http\Controllers\SupportController::class, 'index'])->name('support.index');
        Route::post('/support', [\App\Http\Controllers\SupportController::class, 'store'])->name('support.store');
        
        // Reports routes (all authenticated users)
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/download', [\App\Http\Controllers\ReportController::class, 'download'])->name('reports.download');
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
        Route::get('/metrics', [\App\Http\Controllers\MetricsController::class, 'tenantIndex'])->name('metrics.index');
    });
});
