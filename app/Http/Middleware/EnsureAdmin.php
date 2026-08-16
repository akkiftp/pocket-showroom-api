<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_active || !$request->user()->isSuperAdmin()) {
            return response()->json(['success'=>false,'message'=>'Super Admin access required.'],403);
        }
        return $next($request);
    }
}
