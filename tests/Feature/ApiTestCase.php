<?php

namespace Tests\Feature;

use App\Helpers\CustomToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Base class untuk semua Feature Test API Carbonize.
 *
 * Menyediakan helper untuk autentikasi double (Sanctum + Custom Token)
 * karena API membutuhkan kedua header sekaligus.
 */
abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Prefix semua endpoint API.
     */
    protected string $apiPrefix = '/api/v1';

    /**
     * Membangun URL lengkap endpoint API.
     */
    protected function apiUrl(string $path): string
    {
        return $this->apiPrefix . '/' . ltrim($path, '/');
    }

    /**
     * Membuat user + token, lalu mengembalikan array header yang siap dipakai.
     *
     * Menggunakan $this->withHeaders([...]) atau menambah ke JSON request.
     */
    protected function authHeaders(?User $user = null): array
    {
        $user ??= User::factory()->create();

        // Buat Sanctum token
        $sanctumToken = $user->createToken('api-token')->plainTextToken;
        $tokenId      = explode('|', $sanctumToken)[0];

        // Buat Custom Token
        $customToken = CustomToken::create([
            'user_id'  => $user->id,
            'token_id' => $tokenId,
            'email'    => $user->email,
            'exp'      => time() + (60 * 60 * 24),
        ]);

        return [
            'Authorization' => 'Bearer ' . $sanctumToken,
            'X-Api-Token'   => $customToken,
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Shortcut: buat user baru + dapatkan header auth-nya sekaligus.
     *
     * @return array{user: User, headers: array}
     */
    protected function makeAuthUser(): array
    {
        $user = User::factory()->create();

        return [
            'user'    => $user,
            'headers' => $this->authHeaders($user),
        ];
    }
}
