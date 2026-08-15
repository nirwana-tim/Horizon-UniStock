<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->must_change_password) {
            if ($request->expectsJson() || $request->isMethod('POST')) {
                abort(403, 'Kamu harus mengganti password terlebih dahulu.');
            }

            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
