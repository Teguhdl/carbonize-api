<?php

namespace Tests\Feature\Transport;

use App\Models\TransitVehicle;
use Tests\Feature\ApiTestCase;

/**
 * PublicTransitTest — Pengujian endpoint kendaraan umum:
 *   GET    /api/v1/transport/public/vehicles
 *   GET    /api/v1/transport/public/vehicles/{id}
 *   POST   /api/v1/transport/public/vehicles
 *   PUT    /api/v1/transport/public/vehicles/{id}
 *   DELETE /api/v1/transport/public/vehicles/{id}
 */
class PublicTransitTest extends ApiTestCase
{
    /* =====================================================================
     | INDEX
     * ===================================================================== */

    /** @test */
    public function index_mengembalikan_daftar_kendaraan_umum(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        TransitVehicle::factory()->count(4)->create();

        $response = $this->getJson($this->apiUrl('transport/public/vehicles'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(4, 'data');
    }

    /** @test */
    public function index_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('transport/public/vehicles'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | SHOW
     * ===================================================================== */

    /** @test */
    public function show_mengembalikan_kendaraan_umum_berdasarkan_id(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = TransitVehicle::factory()->create(['name' => 'MRT Jakarta']);

        $response = $this->getJson(
            $this->apiUrl("transport/public/vehicles/{$vehicle->id}"),
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'MRT Jakarta')
                 ->assertJsonPath('data.id', $vehicle->id);
    }

    /** @test */
    public function show_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson(
            $this->apiUrl('transport/public/vehicles/99999'),
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | STORE
     * ===================================================================== */

    /** @test */
    public function store_berhasil_menambah_kendaraan_umum(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/public/vehicles'),
            [
                'name'            => 'Bus Damri',
                'emission_factor' => 0.089,
                'avg_passengers'  => 60,
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Bus Damri');

        $this->assertDatabaseHas('transit_vehicles', ['name' => 'Bus Damri']);
    }

    /** @test */
    public function store_gagal_jika_nama_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/public/vehicles'),
            ['emission_factor' => 0.089, 'avg_passengers' => 60],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_emission_factor_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/public/vehicles'),
            ['name' => 'Bus Test', 'avg_passengers' => 60],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_avg_passengers_kurang_dari_1(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/public/vehicles'),
            ['name' => 'Bus Test', 'emission_factor' => 0.1, 'avg_passengers' => 0],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_tanpa_autentikasi(): void
    {
        $response = $this->postJson(
            $this->apiUrl('transport/public/vehicles'),
            ['name' => 'Bus', 'emission_factor' => 0.1, 'avg_passengers' => 50],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | UPDATE
     * ===================================================================== */

    /** @test */
    public function update_berhasil_mengubah_kendaraan_umum(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = TransitVehicle::factory()->create(['name' => 'Kendaraan Lama']);

        $response = $this->putJson(
            $this->apiUrl("transport/public/vehicles/{$vehicle->id}"),
            ['name' => 'Kendaraan Baru', 'emission_factor' => 0.05],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Kendaraan Baru');
    }

    /** @test */
    public function update_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('transport/public/vehicles/99999'),
            ['name' => 'Nama Baru'],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | DESTROY
     * ===================================================================== */

    /** @test */
    public function destroy_berhasil_menghapus_kendaraan_umum(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = TransitVehicle::factory()->create();

        $response = $this->deleteJson(
            $this->apiUrl("transport/public/vehicles/{$vehicle->id}"),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('transit_vehicles', ['id' => $vehicle->id]);
    }

    /** @test */
    public function destroy_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->deleteJson(
            $this->apiUrl('transport/public/vehicles/99999'),
            [],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }
}
