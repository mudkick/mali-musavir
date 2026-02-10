<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MukellefAuthenticate
{
    /**
     * Mükellef authentication kontrolü yapar.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('mukellef')->check()) {
            return redirect()->route('mukellef-portal.login');
        }

        return $next($request);
    }
}
