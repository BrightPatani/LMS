<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'is_instructor' => \App\Http\Middleware\EnsureInstructor::class,
            'is_student' => \App\Http\Middleware\EnsureStudent::class,
            'is_admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        // 2. Your existing Validation errors (422)
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'success' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        });

        // http exceptions (404, 500, etc.)
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage() ?: 'An error occurred',
            ], $e->getStatusCode());
        });

        // fallback (500)
        $exceptions->render(function (\Throwable $e, $request) {
            return response()->json([
                'success' => 'error',
                'message' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred',
            ], 500);
        });
    })->create();
