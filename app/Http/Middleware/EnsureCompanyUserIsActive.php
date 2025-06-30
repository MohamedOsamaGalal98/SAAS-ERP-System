<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('company')->check() && ! Auth::guard('company')->user()->is_active) {
            Auth::guard('company')->logout();

            return redirect()->route('filament.company.auth.login')
                ->withErrors([
                    'email' => 'Your account is not active. Please contact support.',
                ]);
        }

        return $next($request);
    }
}
