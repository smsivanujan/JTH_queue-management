<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register subscription expiry check globally (runs on all requests)
        $middleware->web(append: [
            \App\Http\Middleware\CheckSubscriptionExpiry::class,
        ]);
        
        // Alias middleware for easier use
        $middleware->alias([
            'tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'tenant.access' => \App\Http\Middleware\EnsureTenantAccess::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'subscription.expiry' => \App\Http\Middleware\CheckSubscriptionExpiry::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'tenant.user' => \App\Http\Middleware\EnsureUserBelongsToTenant::class,
            'queue.auth' => \App\Http\Middleware\AuthorizeQueueAccess::class,
            'clinic.limit' => \App\Http\Middleware\EnforceClinicLimit::class,
            'screen.limit' => \App\Http\Middleware\EnforceScreenLimit::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'service.verify' => \App\Http\Middleware\VerifyServiceAccess::class,
            'superAdmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
