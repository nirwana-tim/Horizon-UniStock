<?php

use App\Http\Middleware\EnsurePasswordChanged;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);

        $middleware->append(ThrottleRequests::using('web'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                if ($e instanceof ThrottleRequestsException) {
                    return response()->json([
                        'error' => 'Terlalu banyak permintaan',
                        'message' => 'Silakan tunggu beberapa saat sebelum mencoba lagi.',
                    ], 429);
                }

                return response()->json([
                    'error' => 'Terjadi kesalahan server',
                    'message' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            if ($e instanceof ThrottleRequestsException) {
                return response()->view('errors.429', [], 429);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->view('errors.404', [], 404);
            }

            if ($e instanceof AuthorizationException) {
                return response()->view('errors.403', ['message' => $e->getMessage()], 403);
            }

            if ($e instanceof TokenMismatchException) {
                return redirect()->back()->with('error', 'Sesi Anda telah berakhir. Silakan coba lagi.');
            }
        });

        $exceptions->reportable(function (Throwable $e) {
            if (config('app.env') === 'production') {
                Log::error($e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        });
    })->create();
