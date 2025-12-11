<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;

class BaseController extends Controller
{
    protected function success($data = null, string $message = 'Success', int $status = 200)
    {
        return ApiResponse::success($data, $message, $status);
    }

    protected function error(string $message, int $status = 400, $errors = null)
    {
        return ApiResponse::error($message, $status, $errors);
    }

    protected function notFound(string $message = 'Data tidak ditemukan')
    {
        return ApiResponse::notFound($message);
    }

    protected function validation($errors, string $message = 'Validasi gagal')
    {
        return ApiResponse::validationError($errors, $message);
    }
}
