<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler{

    public function report(Throwable $e){

        \Illuminate\Support\Facades\Log::error($e->getMessage(), [
            'user_id'   => Auth::id(),
            'ip'        => request()->ip(),
            'userAgent' => request()->userAgent(),
        ]);

        parent::report($e);
    }

    public function render($request, Throwable $e ){

        // If the request expects JSON (API, AJAX, Inertia, etc.)
        if($request->expectsJson()){
           return $this->handleApiException($request, $e);
        }

        // Default Laravel behavior for web requests
        return parent::render($request, $e);
    }

    /**
     * Custom API exception handling.
     */
    protected function handleApiException($request, Throwable $e){
        $status = 500;

        //checks if in local dev or in prod
        $message = app()->isLocal()
        ? $e->getMessage() : "Something went wrong, Please try again letter";

        if($e instanceof ModelNotFoundException){
            $status = 404;
            $message = "Resource not Found!"; // this throwable error for model
        }

        if($e instanceof ValidationValidationException){
            $status = 422;
            return response()->json([
                'success' => false,
                'message' => "Validation Error",
                'errors'  => $e->errors(),
            ],$status);
        }

        if($e instanceof AuthenticationException){
            $status = 401;
            $message = "Unauthenticated Error!";
        }

        if($e instanceof HttpException){
            $status = $e->getStatusCode();
            $message = $e->getMessage() ?: $message;
        }

        return response()->json([
            'success' => false,
            'message' => $message
        ],$status);

    }
}