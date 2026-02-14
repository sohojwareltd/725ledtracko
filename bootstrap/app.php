<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add web middleware group to all web routes
        $middleware->web(append: [
            // Session and auth are already included in web by default
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF token mismatch (Page Expired)
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return back()->withErrors([
                'session' => 'Your session has expired. Please try again.',
            ])->withInput();
        });
    })->create();
