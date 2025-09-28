<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            // Parse and authenticate user from token
            $user = JWTAuth::parseToken()->authenticate();

            if(!$user){
                return response()->json(['error'=>'User not found'],404);
            }

            auth()->setUser($user);

        } catch (TokenExpiredException $e) {
            return response(['error'=>'Token Expired'],401);
        }catch (TokenInvalidException $e){
            return response(['error'=>'Invalid Token'],401);
        }catch (JWTException $e){
            return response(['error'=>'Token not provided'],401);
        }

        return $next($request);
    }
}
