<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\TokenException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request){
         try {
            $login = $this->authService->login(
                $request->only('email', 'password'),$request->ip(),$request->header('User-Agent')

            );
            if(!$login){
                return ApiResponse::error('Invalid Credentials',401);
            }

            if(! Auth::user()->hasVerifiedEmail()){
                return ApiResponse::error('Email not verified. A new verification link has been sent to your email.',403);
            }


            return ApiResponse::success($login, 'Login successful');

        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong', $e->getMessage(), 500);
        }
    }

   public function register(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:150',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return ApiResponse::error(
            'Validation failed',
            $validator->errors(),
            422
        );
    }

    try {
        // Create user
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->sendEmailVerificationNotification();

        return ApiResponse::success(
            $user,
            'User registered. Please check your email for verification link.',
            201
        );

    } catch (\Throwable $e) {
        return ApiResponse::error(
            'Something went wrong while creating user',
            ['exception' => $e->getMessage()],
            500
        );
    }
}


    // public function refresh(Request $request)
    // {
    //     try {
    //         $data = $this->authRepo->refreshToken(
    //             $request->input('refresh_token'),
    //             $request
    //         );

    //         return ApiResponse::success($data, 'Token refreshed');

    //     } catch (TokenException $e) {
    //         return ApiResponse::error($e->getMessage(), 401);
    //     } catch (\Throwable $e) {
    //         return ApiResponse::error('Something went wrong', 500, $e->getMessage());
    //     }
    // }
}
