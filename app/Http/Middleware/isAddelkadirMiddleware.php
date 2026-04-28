<?php

namespace App\Http\Middleware;

use App\Constants\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isAddelkadirMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (int) Auth::user()->role_id === Roles::ADDELKADIR) {
            return $next($request);
        }
        return redirect()->route('login');
    }
}
