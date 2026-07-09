<?php

namespace Tests\Feature\FoodPackaging;

use App\Models\FoodItem;
use Tests\Feature\ApiTestCase;

/**
 * FoodItemTest — Pengujian endpoint master data food & packaging:
 *   GET    /api/v1/food-packaging/items
 *   GET    /api/v1/food-packaging/items/{id}
 *   POST   /api/v1/food-packaging/items
 *   PUT    /api/v1/food-packaging/items/{id}
 *   DELETE /api/v1/food-packaging/items/{id}
 */
class FoodItemTest extends ApiTestCase
{
    /* =====================================================================
     | GET ALL ITEMS (INDEX)
     * ===================================================================== */

    /** @test */
    public function index_mengembalikan_semua_food_items(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        FoodItem::factory()->count(3)->create();

        $response = $this->getJson($this->apiUrl('food-packaging/items'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function index_dapat_filter_berdasarkan_calculation_method_fixed(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        FoodItem::factory()->count(2)->create(['calculation_method' => 'fixed']);
        FoodItem::factory()->climatiq()->count(1)->create();

        $response = $this->getJson(
            $this->apiUrl('food-packaging/items') . '?method=fixed',
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function index_dapat_filter_berdasarkan_calculation_method_climatiq(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        FoodItem::factory()->count(2)->create(['calculation_method' => 'fixed']);
        FoodItem::factory()->climatiq()->count(1)->create();

        $response = $this->getJson(
            $this->apiUrl('food-packaging/items') . '?method=climatiq',
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function index_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('food-packaging/items'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | GET SINGLE ITEM (SHOW)
     * ===================================================================== */

    /** @test */
    public function show_mengembalikan_item_yang_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $item = FoodItem::factory()->create(['name' => 'Ayam Goreng']);

        $response = $this->getJson(
            $this->apiUrl("food-packaging/items/{$item->id}"),
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Ayam Goreng')
                 ->assertJsonPath('data.id', $item->id);
    }

    /** @test */
    public function show_mengembalikan_404_jika_item_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson(
            $this->apiUrl('food-packaging/items/99999'),
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | CREATE ITEM (STORE)
     * ===================================================================== */

    /** @test */
    public function store_berhasil_membuat_item_fixed(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/items'),
            [
                'name'               => 'Nasi Padang',
                'calculation_method' => 'fixed',
                'emission_factor'    => 2.5,
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Nasi Padang')
                 ->assertJsonPath('data.calculation_method', 'fixed');

        $this->assertDatabaseHas('food_items', ['name' => 'Nasi Padang']);
    }

    /** @test */
    public function store_berhasil_membuat_item_climatiq(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/items'),
            [
                'name'               => 'Sate Ayam',
                'calculation_method' => 'climatiq',
                'climatiq_id'        => 'food_packaging-meat-chicken',
            ],
            $headers
        );

        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.calculation_method', 'climatiq');
    }

    /** @test */
    public function store_gagal_jika_nama_tidak_diisi(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/items'),
            ['calculation_method' => 'fixed', 'emission_factor' => 1.0],
            $headers
        );

        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function store_gagal_jika_calculation_method_tidak_valid(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->postJson(
            $this->apiUrl('food-packaging/items'),
            [
                'name'               => 'Item Test',
                'calculation_method' => 'tidak_valid',
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
            $this->apiUrl('food-packaging/items'),
            ['name' => 'Test', 'calculation_method' => 'fixed', 'emission_factor' => 1.0],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | UPDATE ITEM
     * ===================================================================== */

    /** @test */
    public function update_berhasil_mengubah_item(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $item = FoodItem::factory()->create(['name' => 'Nama Lama']);

        $response = $this->putJson(
            $this->apiUrl("food-packaging/items/{$item->id}"),
            ['name' => 'Nama Baru', 'emission_factor' => 3.0],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.name', 'Nama Baru');
    }

    /** @test */
    public function update_mengembalikan_404_jika_item_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->putJson(
            $this->apiUrl('food-packaging/items/99999'),
            ['name' => 'Nama Baru'],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /* =====================================================================
     | DELETE ITEM
     * ===================================================================== */

    /** @test */
    public function destroy_berhasil_menghapus_item(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $item = FoodItem::factory()->create();

        $response = $this->deleteJson(
            $this->apiUrl("food-packaging/items/{$item->id}"),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('food_items', ['id' => $item->id]);
    }

    /** @test */
    public function destroy_mengembalikan_404_jika_item_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->deleteJson(
            $this->apiUrl('food-packaging/items/99999'),
            [],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }
}
