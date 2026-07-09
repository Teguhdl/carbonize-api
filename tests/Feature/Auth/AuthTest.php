<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\ApiTestCase;

/**
 * AuthTest — Pengujian endpoint autentikasi:
 *   POST /api/v1/auth/login
 *   POST /api/v1/auth/register
 *   POST /api/v1/auth/logout
 */
class AuthTest extends ApiTestCase
{
    /* =====================================================================
     | REGISTER
     * ===================================================================== */

    /** @test */
    public function register_berhasil_dengan_data_valid(): void
    {
        $response = $this->postJson($this->apiUrl('auth/register'), [
            'name'     => 'Teguh Dian',
            'email'    => 'teguh@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'name', 'email'],
                 ])
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.email', 'teguh@example.com');

        $this->assertDatabaseHas('users', ['email' => 'teguh@example.com']);
    }

    /** @test */
    public function register_gagal_jika_email_sudah_digunakan(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson($this->apiUrl('auth/register'), [
            'name'     => 'User Lain',
            'email'    => 'duplikat@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function register_gagal_jika_field_kosong(): void
    {
        $response = $this->postJson($this->apiUrl('auth/register'), []);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function register_gagal_jika_nama_kurang_dari_3_karakter(): void
    {
        $response = $this->postJson($this->apiUrl('auth/register'), [
            'name'     => 'AB',
            'email'    => 'valid@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function register_gagal_jika_password_kurang_dari_5_karakter(): void
    {
        $response = $this->postJson($this->apiUrl('auth/register'), [
            'name'     => 'Valid Name',
            'email'    => 'valid2@example.com',
            'password' => '123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | LOGIN
     * ===================================================================== */

    /** @test */
    public function login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson($this->apiUrl('auth/login'), [
            'email'    => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user'           => ['id', 'name', 'email'],
                         'sanctum_token',
                         'custom_token',
                     ],
                 ])
                 ->assertJsonPath('success', true);
    }

    /** @test */
    public function login_gagal_jika_email_tidak_terdaftar(): void
    {
        $response = $this->postJson($this->apiUrl('auth/login'), [
            'email'    => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function login_gagal_jika_password_salah(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('benar123'),
        ]);

        $response = $this->postJson($this->apiUrl('auth/login'), [
            'email'    => 'user@example.com',
            'password' => 'salah123',
        ]);

        $response->assertStatus(400)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function login_gagal_jika_field_kosong(): void
    {
        $response = $this->postJson($this->apiUrl('auth/login'), []);

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | LOGOUT
     * ===================================================================== */

    /** @test */
    public function logout_berhasil_dengan_token_valid(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('auth/logout'),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('message', 'Logout berhasil');
    }

    /** @test */
    public function logout_gagal_tanpa_autentikasi(): void
    {
        $response = $this->postJson(
            $this->apiUrl('auth/logout'),
            [],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }
}
