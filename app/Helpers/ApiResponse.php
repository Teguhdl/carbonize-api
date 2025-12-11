<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, string $message = 'Success', int $status = 200)
    {
        return response()->json([
            'success'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(string $message, int $status = 400, $errors = null)
    {
        return response()->json([
            'success'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    public static function validationError($errors, string $message = 'Validasi gagal')
    {
        return response()->json([
            'success'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }

    public static function notFound(string $message = 'Data tidak ditemukan')
    {
        return response()->json([
            'success'  => false,
            'message' => $message,
        ], 404);
    }
}
