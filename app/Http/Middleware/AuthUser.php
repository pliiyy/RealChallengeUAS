<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Jika request API / AJAX → JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Jika request web → redirect ke login
            return redirect('/login');
        }

        return $next($request);
    }
}
