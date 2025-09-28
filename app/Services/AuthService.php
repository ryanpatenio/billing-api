<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService{

    private $authRepo;

    public function __construct(AuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function register(array $data):User {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $user;
    }

    public function login(array $credentials, string $ip, string $userAgent): ?array{
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        $user = auth()->user();

         // Check if a valid refresh token exists
        $refreshTokenModel = $this->authRepo->isRefreshTokenValid($user->id);

        if(!$refreshTokenModel){
            //create another refreshToken
            $refreshTokenModel = RefreshToken::create([
            'user_id'    => $user->id,
            'token'      => bin2hex(random_bytes(40)),
            'expires_at' => now()->addDays(7),
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
        }
      
        return [
            'access_token'  => $token,
            'token_type'    => 'bearer',
            'expires_in'    => JWTAuth::factory()->getTTL() * 60,
            'refresh_token' => $refreshTokenModel->token
        ];
    }

}