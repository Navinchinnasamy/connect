<?php

namespace App\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Entities\Users;

class TokenAfterMiddleware
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
        $response = $next($request);
        $token = JWTAuth::getToken();
        $parsed = JWTAuth::getPayload($token);
        $user = new Users();
        $user->empCode = $parsed->get('empCode');
        $user->_id = $parsed->get('userId');
        $token = JWTAuth::fromUser($user);
        $response->header('token', $token);

        return $response;
    }
}
