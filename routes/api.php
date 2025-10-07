<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\v1\AuthController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',[AuthController::class,'login']);

    Route::get('/email/verify/{id}/{hash}',function($id,$hash,Request $request){
        $user = User::findOrFail($id);

        //check if user is already verified
        if($user->hasVerifiedEmail()){
            
            return ApiResponse::error('Email is already verified.',201);
        }
        // Validate hash (prevents forged requests)
        if(! hash_equals(sha1($user->getEmailForVerification()),$hash)){
            return ApiResponse::error('Invalid verification link',400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        return ApiResponse::success('Email verified successfully!',201);
        
    })->middleware(['signed'])->name('verification.verify');

    Route::middleware('jwt.auth')->group(function(){
        Route::post('/email/resend',function(Request $request){
            if($request->user()->hasVerifiedEmail()){
                return ApiResponse::error('Email is already verified',200);
            }
            $request->user()->sendEmailVerificationNotification();

            //response
            return ApiResponse::success('Verification Link sent, Please check your Email');
        })->name('verification.send');
    });
});