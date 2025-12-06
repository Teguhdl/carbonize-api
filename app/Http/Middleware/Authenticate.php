<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        return response()->json([
            'success' => false,
            'message' => 'Tidak memiliki akses, silakan login.',
        ], 401);
    }
}
