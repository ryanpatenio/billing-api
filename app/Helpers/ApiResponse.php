<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success(mixed $data = null,string $message = 'Success',int $status = 200 ) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Error response
     */
    public static function error(string $message = 'Error',mixed $errors = null,int $status = 400) {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
