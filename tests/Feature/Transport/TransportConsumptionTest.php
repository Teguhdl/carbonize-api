<?php

namespace Tests\Feature\Transport;

use App\Models\FuelType;
use App\Models\TransitVehicle;
use App\Models\VehicleType;
use Tests\Feature\ApiTestCase;

/**
 * TransportConsumptionTest — Pengujian endpoint entri konsumsi transportasi:
 *   POST /api/v1/transport/entries
 *
 * Menangani dua mode:
 *   - private: kendaraan pribadi (vehicle_type_id + fuel_type_id)
 *   - public:  kendaraan umum (transit_vehicle_id)
 */
class TransportConsumptionTest extends ApiTestCase
{
    /* =====================================================================
     | PRIVATE VEHICLE
     * ===================================================================== */

    /** @test */
    public function store_berhasil_mencatat_konsumsi_kendaraan_pribadi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create(['default_efficiency' => 15.0]);
        $fuel    = FuelType::factory()->create(['emission_factor' => 2.35]);

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'            => 'private',
                'vehicle_type_id' => $vehicle->id,
                'fuel_type_id'    => $fuel->id,
                'quantity'        => 10.0,
                'entry_date'      => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.entry_type', 'private_vehicle')
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'entry_type',
                         'quantity',
                         'emissions',
                         'vehicle_type',
                         'fuel_type',
                     ],
                 ]);

        $this->assertDatabaseHas('consumption_entries', [
            'entry_type'      => 'private_vehicle',
            'vehicle_type_id' => $vehicle->id,
            'fuel_type_id'    => $fuel->id,
        ]);
    }

    /** @test */
    public function store_berhasil_mencatat_konsumsi_kendaraan_pribadi_dengan_custom_efficiency(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create(['default_efficiency' => 15.0]);
        $fuel    = FuelType::factory()->create(['emission_factor' => 2.35]);

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'              => 'private',
                'vehicle_type_id'   => $vehicle->id,
                'fuel_type_id'      => $fuel->id,
                'quantity'          => 10.0,
                'entry_date'        => now()->format('Y-m-d'),
                'custom_efficiency' => 20.0, // override default
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true);

        // SQLite dapat mengembalikan nilai sebagai integer/float,
        // jadi bandingkan secara longgar (20 == 20.0).
        $this->assertEquals(20.0, $response->json('data.custom_efficiency'));
    }

    /* =====================================================================
     | PUBLIC TRANSIT
     * ===================================================================== */

    /** @test */
    public function store_berhasil_mencatat_konsumsi_kendaraan_umum(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $transit = TransitVehicle::factory()->create([
            'emission_factor' => 0.089,
            'avg_passengers'  => 60,
        ]);

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'               => 'public',
                'transit_vehicle_id' => $transit->id,
                'quantity'           => 5.0,
                'entry_date'         => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.entry_type', 'public_transit')
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'entry_type',
                         'quantity',
                         'emissions',
                         'transit_vehicle',
                     ],
                 ]);

        $this->assertDatabaseHas('consumption_entries', [
            'entry_type'         => 'public_transit',
            'transit_vehicle_id' => $transit->id,
        ]);
    }

    /* =====================================================================
     | VALIDASI GAGAL
     * ===================================================================== */

    /** @test */
    public function store_gagal_jika_mode_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            ['quantity' => 10.0, 'entry_date' => now()->format('Y-m-d')],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_mode_tidak_valid(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'       => 'bukan_mode',
                'quantity'   => 10.0,
                'entry_date' => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_private_mode_tanpa_vehicle_type_id(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $fuel = FuelType::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'         => 'private',
                'fuel_type_id' => $fuel->id,
                'quantity'     => 10.0,
                'entry_date'   => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_private_mode_tanpa_fuel_type_id(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'            => 'private',
                'vehicle_type_id' => $vehicle->id,
                'quantity'        => 10.0,
                'entry_date'      => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_public_mode_tanpa_transit_vehicle_id(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'       => 'public',
                'quantity'   => 5.0,
                'entry_date' => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_quantity_nol(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $vehicle = VehicleType::factory()->create();
        $fuel    = FuelType::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            [
                'mode'            => 'private',
                'vehicle_type_id' => $vehicle->id,
                'fuel_type_id'    => $fuel->id,
                'quantity'        => 0,
                'entry_date'      => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_tanpa_autentikasi(): void
    {
        $response = $this->postJson(
            $this->apiUrl('transport/entries'),
            ['mode' => 'private', 'quantity' => 10.0, 'entry_date' => now()->format('Y-m-d')],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }
}
