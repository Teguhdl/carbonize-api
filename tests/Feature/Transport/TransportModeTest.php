<?php

namespace Tests\Feature\Transport;

use Tests\Feature\ApiTestCase;

/**
 * TransportModeTest — Pengujian endpoint daftar mode transportasi:
 *   GET /api/v1/transport/modes
 */
class TransportModeTest extends ApiTestCase
{
    /** @test */
    public function index_mengembalikan_dua_mode_transportasi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson($this->apiUrl('transport/modes'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(2, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['mode', 'label', 'description', 'icon'],
                     ],
                 ]);

        // Pastikan mode 'private' dan 'public' ada
        $modes = collect($response->json('data'))->pluck('mode');
        $this->assertTrue($modes->contains('private'));
        $this->assertTrue($modes->contains('public'));
    }

    /** @test */
    public function index_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('transport/modes'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }
}
