<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWebAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_authenticated') === true) {
            return $next($request);
        }

        if (auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->is_admin)) {
            session(['admin_authenticated' => true]);
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect('/admin/login')->with('error', 'Please log in with Super Admin credentials.');
    }
}
