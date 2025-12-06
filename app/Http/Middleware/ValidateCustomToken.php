<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CustomToken;
use App\Models\User;

class ValidateCustomToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Api-Token');

        if (!$token) {
            return response()->json(['message' => 'Custom token required'], 401);
        }

        try {
            $data = CustomToken::validate($token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid token format'], 401);
        }

        if (!$data || !isset($data['user_id'])) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }

        $user = User::find($data['user_id']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->merge([
            'custom_user_id' => $user->id,
            'custom_token_data' => $data
        ]);

        return $next($request);
    }
}
