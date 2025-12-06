<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Routing\RouteNotFoundException;


class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode request tidak diizinkan untuk endpoint ini.',
                ], 405);
            }

            if (
                $exception instanceof NotFoundHttpException ||
                $exception instanceof RouteNotFoundException
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Terjadi kesalahan pada server.',
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
