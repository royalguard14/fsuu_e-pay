<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // If the user is a Developer, always allow access
        if (Auth::check() && Auth::user()->role->role_name === 'Developer') {
            return $next($request);
        }

        // Check if the user's role is in the allowed roles
        if (Auth::check() && in_array(Auth::user()->role->role_name, $roles)) {
            return $next($request);
        }

        // Redirect if access is denied
        return redirect('/error')->with('error', 'Access denied');
    }
}
