<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/')->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT',
            ]);
        }

        return $next($request);
    }
}
