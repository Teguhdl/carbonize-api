<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{

    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'Data tidak valid');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah', 400);
        }

        $sanctumToken = $user->createToken('api-token')->plainTextToken;
        $customToken = \App\Helpers\CustomToken::create([
            'user_id' => $user->id,
            'token_id' => explode('|', $sanctumToken)[0], // Sanctum token ID
            'email' => $user->email,
            'exp' => time() + (60 * 60 * 24), // 24 jam expired
        ]);

        return $this->success([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
            ],
            'sanctum_token' => $sanctumToken,
            'custom_token'  => $customToken,
        ], 'Login berhasil');
    }


    public function register(Request $request)
    {
        $validator = validator($request->all(), [
            'name'     => 'required|min:3',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'Data tidak valid');
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'CreatedAt' => now(),
        ]);

        return $this->success([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ], 'Register berhasil', 201);
    }
}
