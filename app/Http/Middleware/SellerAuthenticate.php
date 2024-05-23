<?php

namespace App\Http\Middleware;

use Closure;

class SellerAuthenticate
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
        if (!auth()->guard('seller')->check()) {
            return redirect(route('login'));
        }

        return $next($request);
    }
}
