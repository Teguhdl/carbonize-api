<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    // ---------------------------------------------------
    // GET /user/profile
    // ---------------------------------------------------
    public function profile(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        return $this->success($user, 'Profil user ditemukan');
    }

    // ---------------------------------------------------
    // POST /user/change-password
    // ---------------------------------------------------
    public function changePassword(Request $request)
    {
        $validator = validator($request->all(), [
            'user_id'      => 'required',
            'old_password' => 'required',
            'new_password' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'Password lama salah atau password baru tidak valid');
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->error('Password lama salah atau password baru tidak valid', 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->success(null, 'Password berhasil diperbarui');
    }
}
