<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\ApiTestCase;

/**
 * UserTest — Pengujian endpoint profil user:
 *   GET  /api/v1/user/profile
 *   PUT  /api/v1/user/profile
 *   POST /api/v1/user/change-password
 *   POST /api/v1/user/profile/image
 */
class UserTest extends ApiTestCase
{
    /* =====================================================================
     | GET PROFILE
     * ===================================================================== */

    /** @test */
    public function get_profile_berhasil_dengan_token_valid(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson($this->apiUrl('user/profile'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.email', $user->email)
                 ->assertJsonPath('data.name', $user->name);
    }

    /** @test */
    public function get_profile_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('user/profile'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | UPDATE PROFILE
     * ===================================================================== */

    /** @test */
    public function update_profile_berhasil_mengubah_nama(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('user/profile'),
            ['name' => 'Nama Baru Saya'],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Nama Baru Saya');
    }

    /** @test */
    public function update_profile_berhasil_mengubah_daily_carbon_limit(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('user/profile'),
            ['dailyCarbonLimit' => 50],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.dailyCarbonLimit', 50);
    }

    /** @test */
    public function update_profile_gagal_jika_nama_kurang_3_karakter(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('user/profile'),
            ['name' => 'AB'],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function update_profile_gagal_tanpa_autentikasi(): void
    {
        $response = $this->putJson(
            $this->apiUrl('user/profile'),
            ['name' => 'Nama'],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | CHANGE PASSWORD
     * ===================================================================== */

    /** @test */
    public function change_password_berhasil_dengan_data_valid(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $headers = $this->authHeaders($user);

        $response = $this->postJson(
            $this->apiUrl('user/change-password'),
            [
                'old_password' => 'password_lama',
                'new_password' => 'password_baru123',
            ],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        // Verifikasi password benar-benar berubah di database
        $user->refresh();
        $this->assertTrue(Hash::check('password_baru123', $user->password));
    }

    /** @test */
    public function change_password_gagal_jika_password_lama_salah(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $headers = $this->authHeaders($user);

        $response = $this->postJson(
            $this->apiUrl('user/change-password'),
            [
                'old_password' => 'salah_password',
                'new_password' => 'password_baru123',
            ],
            $headers
        );

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function change_password_gagal_jika_field_kosong(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('user/change-password'),
            [],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function change_password_gagal_jika_password_baru_terlalu_pendek(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password_lama'),
        ]);

        $headers = $this->authHeaders($user);

        $response = $this->postJson(
            $this->apiUrl('user/change-password'),
            [
                'old_password' => 'password_lama',
                'new_password' => '123',
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | UPLOAD PROFILE IMAGE
     * ===================================================================== */

    /** @test */
    public function upload_profile_image_berhasil(): void
    {
        Storage::fake('public');

        ['headers' => $headers] = $this->makeAuthUser();

        $file = UploadedFile::fake()->image('profile.jpg', 100, 100);

        $response = $this->postJson(
            $this->apiUrl('user/profile/image'),
            ['image' => $file],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => ['profileImage', 'profileImageUrl'],
                 ]);
    }

    /** @test */
    public function upload_profile_image_gagal_jika_tidak_ada_file(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('user/profile/image'),
            [],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }
}
