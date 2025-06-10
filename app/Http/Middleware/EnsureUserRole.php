<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'user' || $user->email === 'centralakun@samarent.com') {
            return $next($request);
            }
        }

        abort(403);
    }
}
