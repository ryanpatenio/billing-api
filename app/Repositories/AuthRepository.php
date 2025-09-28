<?php

namespace App\Repositories;

use App\Models\RefreshToken;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthRepositoryInterface {

    /**
     * 
     * @param array $credentials
     * @param Request $request
     * @return array
     * @throws AuthenticationException
     */


    /**
     * Refresh access token using a refresh token
     *
     * @param string $refreshToken
     * @return array
     * @throws TokenException
     */
    public function refreshToken(string $refreshToken, Request $request): array
    {
        $stored = RefreshToken::where('token', $refreshToken)->first();

        if (!$stored || $stored->isExpired()) {
            throw new TokenException('Refresh token expired or invalid');
        }

        $user = $stored->user;

        $newAccessToken = JWTAuth::fromUser($user);

        return [
            'access_token'  => $newAccessToken,
            'token_type'    => 'bearer',
            'expires_in'    => JWTAuth::factory()->getTTL() * 60,
            'refresh_token' => $refreshToken, // reuse same refresh token
        ]; 
    }

    public function isRefreshTokenValid(string $user_id){
        $token = RefreshToken::where('user_id',$user_id)
        ->where('expires_at','>',Carbon::now())
        ->where('revoked','!=','1')
        ->first();
        
        return $token ?: null;
    }

    
}