<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureMekanikRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'mekanik' || $user->isSuperAdmin()) {
            return $next($request);
            }
        }

        abort(403);
    }
}
