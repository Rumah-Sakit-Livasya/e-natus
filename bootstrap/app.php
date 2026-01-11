<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
<<<<<<< HEAD
        //
=======
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->web(append: [
            'throttle:120,1',
        ]);
>>>>>>> 41a31ad1a8a01d6fb3f70df969516c7d431da7ea
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
