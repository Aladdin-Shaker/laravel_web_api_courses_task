<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckTeacher
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
        // Auth::user()->roles->first()->role !== 'admin' ||
        if (Auth::user()->roles->first()->role == 'teacher' || Auth::user()->roles->first()->role == 'admin') {
            return $next($request);
        }
        return response(json_encode(['error' => 'Unauthorised']), 401)
            ->header('Content-Type', 'text/json');
    }
}
