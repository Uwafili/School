<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GoogleAuthenticateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user has Google session or Laravel Auth
        if (Session::has('authenticated') || auth()->check()) {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Please sign in first');
    }
}
