<?php

// bootstrap/app.php (Laravel 11)
// OR for Laravel 10, add to app/Http/Kernel.php in $middlewareAliases array

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
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

/* 
FOR LARAVEL 10:
In app/Http/Kernel.php, add to $middlewareAliases array:

protected $middlewareAliases = [
    // ... other middleware
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
];
*/