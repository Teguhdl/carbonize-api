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

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('Password salah', 400);
        }

        $token = base64_encode($user->email . '|' . now());

        $user->lastLogin = now();
        $user->save();

        return $this->success([
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'lastLogin' => $user->lastLogin,
            'token'     => $token,
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
