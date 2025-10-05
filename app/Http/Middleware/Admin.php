<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        // You can adjust this condition based on your app
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Access denied. Admins only.');
        }

        return $next($request);
    }
}
