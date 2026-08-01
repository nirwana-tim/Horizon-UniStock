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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'password.changed' => \App\Http\Middleware\EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan server',
                    'message' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return response()->view('errors.404', [], 404);
            }

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return response()->view('errors.403', ['message' => $e->getMessage()], 403);
            }

            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->back()->with('error', 'Sesi Anda telah berakhir. Silakan coba lagi.');
            }
        });

        $exceptions->reportable(function (Throwable $e) {
            if (config('app.env') === 'production') {
                \Illuminate\Support\Facades\Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        });
    })->create();
