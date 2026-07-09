<?php

namespace Tests\Feature\Transport;

use App\Models\FuelType;
use App\Models\VehicleType;
use Tests\Feature\ApiTestCase;

/**
 * PrivateVehicleTest — Pengujian endpoint kendaraan pribadi & bahan bakar:
 *
 * Vehicle Types:
 *   GET    /api/v1/transport/private/vehicles
 *   POST   /api/v1/transport/private/vehicles
 *   PUT    /api/v1/transport/private/vehicles/{id}
 *   DELETE /api/v1/transport/private/vehicles/{id}
 *
 * Fuel Types:
 *   GET    /api/v1/transport/private/fuels
 *   POST   /api/v1/transport/private/fuels
 *   PUT    /api/v1/transport/private/fuels/{id}
 *   DELETE /api/v1/transport/private/fuels/{id}
 */
class PrivateVehicleTest extends ApiTestCase
{
    /* =====================================================================
     | VEHICLE TYPES
     * ===================================================================== */

    /** @test */
    public function get_vehicles_mengembalikan_daftar_kendaraan(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        VehicleType::factory()->count(3)->create();

        $response = $this->getJson($this->apiUrl('transport/private/vehicles'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function get_vehicles_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('transport/private/vehicles'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function store_vehicle_berhasil_menambah_kendaraan(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/private/vehicles'),
            ['name' => 'Motor Matic', 'default_efficiency' => 40.5],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Motor Matic');

        $this->assertDatabaseHas('vehicle_types', ['name' => 'Motor Matic']);
    }

    /** @test */
    public function store_vehicle_gagal_jika_nama_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/private/vehicles'),
            ['default_efficiency' => 40.5],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_vehicle_gagal_jika_efficiency_tidak_valid(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/private/vehicles'),
            ['name' => 'Motor Matic', 'default_efficiency' => 0],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function update_vehicle_berhasil_mengubah_kendaraan(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create(['name' => 'Motor Lama']);

        $response = $this->putJson(
            $this->apiUrl("transport/private/vehicles/{$vehicle->id}"),
            ['name' => 'Motor Baru', 'default_efficiency' => 50.0],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Motor Baru');
    }

    /** @test */
    public function update_vehicle_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('transport/private/vehicles/99999'),
            ['name' => 'Nama Baru'],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function destroy_vehicle_berhasil_menghapus_kendaraan(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create();

        $response = $this->deleteJson(
            $this->apiUrl("transport/private/vehicles/{$vehicle->id}"),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('vehicle_types', ['id' => $vehicle->id]);
    }

    /** @test */
    public function destroy_vehicle_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->deleteJson(
            $this->apiUrl('transport/private/vehicles/99999'),
            [],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | FUEL TYPES
     * ===================================================================== */

    /** @test */
    public function get_fuels_mengembalikan_daftar_bahan_bakar(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        FuelType::factory()->count(3)->create();

        $response = $this->getJson($this->apiUrl('transport/private/fuels'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function get_fuels_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('transport/private/fuels'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function store_fuel_berhasil_menambah_bahan_bakar(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/private/fuels'),
            ['name' => 'Pertamax Turbo', 'emission_factor' => 2.31],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Pertamax Turbo');

        $this->assertDatabaseHas('fuel_types', ['name' => 'Pertamax Turbo']);
    }

    /** @test */
    public function store_fuel_gagal_jika_field_tidak_lengkap(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/private/fuels'),
            ['name' => 'Pertalite'],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function update_fuel_berhasil_mengubah_bahan_bakar(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $fuel = FuelType::factory()->create(['name' => 'Bahan Bakar Lama']);

        $response = $this->putJson(
            $this->apiUrl("transport/private/fuels/{$fuel->id}"),
            ['name' => 'Bahan Bakar Baru', 'emission_factor' => 1.5],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Bahan Bakar Baru');
    }

    /** @test */
    public function update_fuel_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('transport/private/fuels/99999'),
            ['name' => 'Nama Baru'],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function destroy_fuel_berhasil_menghapus_bahan_bakar(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $fuel = FuelType::factory()->create();

        $response = $this->deleteJson(
            $this->apiUrl("transport/private/fuels/{$fuel->id}"),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('fuel_types', ['id' => $fuel->id]);
    }

    /** @test */
    public function destroy_fuel_mengembalikan_404_jika_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->deleteJson(
            $this->apiUrl('transport/private/fuels/99999'),
            [],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }
}
