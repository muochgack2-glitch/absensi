<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CSRF exceptions: only for external webhooks (e.g. inbound WhatsApp provider callbacks)
        // Gateway admin routes (restart/logout/reset/toggle-failover) are intentionally NOT excluded
        // to protect against CSRF attacks from malicious third-party sites.
        // $middleware->validateCsrfTokens(except: []); // no exceptions needed currently

        // Role-based access middleware
        $middleware->alias([
            'role'        => \App\Http\Middleware\WaliKelasMiddleware::class,
            'ekaldik.api' => \App\Http\Middleware\ValidateEkaldikApiKey::class,
            'phone.api'   => \App\Http\Middleware\ValidatePhoneApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
