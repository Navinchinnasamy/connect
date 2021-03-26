<?php

namespace App\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenBeforeMiddleware
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
        if($request->header('Authorization') && $request->bearerToken()){
            $token = JWTAuth::getToken();
            $parsed = JWTAuth::getPayload($token);
            $request->merge(['empCode' => $parsed->get('empCode'), 'userId' => $parsed->get('userId')]);
        } else {
            return response(["code" => 401, "data" => ["message" => "Unauthorized", "status" => "error"]], 401);
        }

        return $next($request);
    }
}
