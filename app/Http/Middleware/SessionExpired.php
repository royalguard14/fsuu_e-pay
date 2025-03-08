<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionExpired
{
    public function handle($request, Closure $next)
    {
        // Check if user is logged in and session has expired
        if (Auth::check() && !Session::has('last_activity')) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        // Update session activity timestamp
        Session::put('last_activity', now());

        return $next($request);
    }
}
