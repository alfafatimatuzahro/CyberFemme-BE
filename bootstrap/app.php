<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'operational.hours' => \App\Http\Middleware\EnsureWithinOperationalHours::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle autentikasi error
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Token tidak valid atau sudah kadaluarsa.'], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Data tidak valid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
        });
    })->create();
