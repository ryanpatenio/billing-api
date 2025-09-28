<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Facades\Request;

interface AuthRepositoryInterface {
    
    public function refreshToken(string $refreshToken, Request $request): array;

    //public function logout(string $refreshToken): bool;
}