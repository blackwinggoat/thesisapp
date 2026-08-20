<?php

namespace App\Http\Middleware;

use Closure;

class kaprodi
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
        if (auth()->check() && in_array((int) $request->user()->level, [1, 5], true)) {
            return $next($request);
        }
        return redirect()->guest('/');
    }
}
