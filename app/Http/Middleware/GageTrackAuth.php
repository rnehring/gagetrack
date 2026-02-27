<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GageTrackAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user')) {
            session(['intended_url' => $request->url()]);
            return redirect()->route('login');
        }

        return $next($request);
    }
}
