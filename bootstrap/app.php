<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // App-specific middleware aliases.
        // 'is_admin'     — used by tenant admin routes (panel #2)
        // 'super_admin'  — used by super admin routes on central domain (panel #4)
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'super_admin' => \App\Http\Middleware\IsSuperAdmin::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

   