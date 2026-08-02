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
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\IsAdmin::class,
            'super_admin' => \App\Http\Middleware\IsSuperAdmin::class,
        ]);

        // Prepend MemberSessionCookie so it runs BEFORE StartSession.
        // For member/* routes it swaps the session cookie name, giving
        // members a completely separate session from admins.
        $middleware->prepend(\App\Http\Middleware\MemberSessionCookie::class);

        // Append PreventDevCache to the web middleware group so it runs
        // on every web request. In local env it sets no-cache headers
        // so the browser always fetches fresh content after git pull.
        $middleware->web(append: [
            \App\Http\Middleware\PreventDevCache::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

   