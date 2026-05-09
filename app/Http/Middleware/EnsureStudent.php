<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role->value === 'student') {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'You are not a student.',
        ], 403);

        if (!$user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been deactivated.',
            ], 403);
        }
    }
}