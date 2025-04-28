<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForApi
{
    public function handle(Request $request, Closure $next)
    {
        // Nonaktifkan CSRF untuk API
        return $next($request);
    }
}
