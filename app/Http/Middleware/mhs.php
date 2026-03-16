<?php

namespace App\Http\Middleware;

use Closure;

class mhs
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
        if (auth()->check() && ($request->user()->level==8)) {
            return $next($request);
        }
        return redirect()->guest('/');
    }
}
