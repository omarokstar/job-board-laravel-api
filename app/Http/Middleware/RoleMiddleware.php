<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> 87dd1c3 (post a job or blog)
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
<<<<<<< HEAD
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized. Please login.'], 401);
        }

        if (Auth::user()->role !== $role) {
            return response()->json(['error' => 'Unauthorized. Required role: ' . $role], 403);
        }

=======
    public function handle(Request $request, Closure $next): Response
    {
>>>>>>> 87dd1c3 (post a job or blog)
        return $next($request);
    }
}
