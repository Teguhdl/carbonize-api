<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{

    public function profile(Request $request)
    {
        $userId = $request->custom_user_id;

        $user = User::find($userId);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        return $this->success($user, 'Profil user ditemukan');
    }

    public function changePassword(Request $request)
    {
        $validator = validator($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'Password lama salah atau password baru tidak valid');
        }

        $user = User::find($request->custom_user_id);

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

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'sometimes|min:3',
            'dailyCarbonLimit' => 'sometimes|numeric|min:1',
            'dateOfBirth' => 'sometimes|date',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'Data tidak valid');
        }

        $user = User::find($request->custom_user_id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        // Update only provided fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('dailyCarbonLimit')) {
            $user->dailyCarbonLimit = $request->dailyCarbonLimit;
        }
        if ($request->has('dateOfBirth')) {
            $user->dateOfBirth = $request->dateOfBirth;
        }

        $user->save();

        return $this->success($user, 'Profil berhasil diperbarui');
    }

    /**
     * Upload profile image
     */
    public function uploadProfileImage(Request $request)
    {
        $validator = validator($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->validation($validator->errors(), 'File tidak valid');
        }

        $user = User::find($request->custom_user_id);

        if (!$user) {
            return $this->notFound('User tidak ditemukan');
        }

        // Delete old image if exists
        if ($user->profileImage && file_exists(public_path('uploads/profiles/' . $user->profileImage))) {
            unlink(public_path('uploads/profiles/' . $user->profileImage));
        }

        // Save new image
        $image = $request->file('image');
        $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/profiles'), $imageName);

        $user->profileImage = $imageName;
        $user->save();

        return $this->success([
            'profileImage' => $imageName,
            'profileImageUrl' => url('uploads/profiles/' . $imageName),
        ], 'Foto profil berhasil diupload');
    }
}
