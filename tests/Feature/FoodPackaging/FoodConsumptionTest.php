<?php

namespace Tests\Feature\FoodPackaging;

use App\Models\FoodItem;
use Tests\Feature\ApiTestCase;

/**
 * FoodConsumptionTest — Pengujian endpoint entri konsumsi makanan:
 *   POST /api/v1/food-packaging/entries
 */
class FoodConsumptionTest extends ApiTestCase
{
    /** @test */
    public function store_berhasil_mencatat_konsumsi_makanan(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        // Buat food item dengan emission_factor agar kalkulasi berjalan
        $foodItem = FoodItem::factory()->create([
            'calculation_method' => 'fixed',
            'emission_factor'    => 2.5,
        ]);

        $response = $this->postJson(
            $this->apiUrl('food-packaging/entries'),
            [
                'food_item_id' => $foodItem->id,
                'quantity'     => 1.5,
                'entry_date'   => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'entry_type',
                         'quantity',
                         'emissions',
                         'entry_date',
                         'food_item',
                     ],
                 ])
                 ->assertJsonPath('data.entry_type', 'food');

        $this->assertDatabaseHas('consumption_entries', [
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
            'quantity'     => 1.5,
        ]);
    }

    /** @test */
    public function store_gagal_jika_food_item_id_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/entries'),
            [
                'food_item_id' => 99999,
                'quantity'     => 1.0,
                'entry_date'   => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_quantity_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/entries'),
            [
                'food_item_id' => $foodItem->id,
                'entry_date'   => now()->format('Y-m-d'),
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_entry_date_tidak_valid(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/entries'),
            [
                'food_item_id' => $foodItem->id,
                'quantity'     => 1.0,
                'entry_date'   => 'bukan-tanggal',
            ],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_quantity_nol_atau_negatif(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/entries'),
            [
                'food_item_id' => $foodItem->id,
                'quantity'     => 0,
                'entry_date'   => now()->format('Y-m-d'),
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
            $this->apiUrl('food-packaging/entries'),
            ['food_item_id' => 1, 'quantity' => 1.0, 'entry_date' => now()->format('Y-m-d')],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }
}
