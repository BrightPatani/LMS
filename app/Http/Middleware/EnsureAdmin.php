<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || $user->role->value !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Access denied. Admins only.',
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account has been deactivated.',
            ], 403);
        }
        return $next($request);
    }
}
