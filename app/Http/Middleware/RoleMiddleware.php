<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please log in first.');
        }
    
        if (Auth::user()->hasRole($role)) {
            return $next($request);
        }
    
        return redirect('/home')->with('error', 'Unauthorized access.');
    }
}
