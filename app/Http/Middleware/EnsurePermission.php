<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (!$user || !$user->is_active || !$user->canDo($permission)) {
            return response()->json(['success'=>false,'message'=>'Permission denied: '.$permission], 403);
        }
        return $next($request);
    }
}
