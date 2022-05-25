<?php

namespace App\Http\Middleware\Api;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class Authenticated
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
        $apiToken = $request->api_token;
        $user = User::where('api_token', $apiToken)->first();

        if ($user === null) {
            return response()->json('User not found.', 401);
        }

        // Pass user data to the next middleware so it doesn't need to make another SQL request.
        $request->merge([
            'api_user' => $user
        ]);

        return $next($request);
    }
}
