<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Accès réservé au SuperAdmin.');
            }
            return redirect()->route('login')->with('error', 'Accès réservé à la direction DjaDja.');
        }

        return $next($request);
    }
}
