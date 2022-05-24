<?php

namespace App\Http\Middleware\Api;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class IsAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // api_user parameter is set by the Authenticated middleware.
        if ($request->api_user->is_author) {
            return abort(401);
        }

        return $next($request);
    }
}
