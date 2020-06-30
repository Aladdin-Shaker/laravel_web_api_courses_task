<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->roles->first()->role !== 'admin') {
            return response(json_encode(['error' => 'Unauthorised']), 401)
                ->header('Content-Type', 'text/json');
        }

        return $next($request);
    }
}
