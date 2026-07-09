<?php

namespace Tests\Feature\ConsumptionHistory;

use App\Models\ConsumptionEntry;
use App\Models\FoodItem;
use App\Models\User;
use Tests\Feature\ApiTestCase;

/**
 * ConsumptionHistoryTest — Pengujian endpoint riwayat konsumsi:
 *   GET    /api/v1/entries
 *   GET    /api/v1/entries/{id}
 *   DELETE /api/v1/entries/{id}
 */
class ConsumptionHistoryTest extends ApiTestCase
{
    /* =====================================================================
     | INDEX — Daftar semua entri milik user
     * ===================================================================== */

    /** @test */
    public function index_mengembalikan_riwayat_konsumsi_milik_user(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        // Buat 3 entri milik user yang login
        ConsumptionEntry::factory()->count(3)->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
        ]);

        // Buat 2 entri milik user lain (tidak boleh muncul)
        $otherUser = User::factory()->create();
        ConsumptionEntry::factory()->count(2)->create([
            'user_id'      => $otherUser->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
        ]);

        $response = $this->getJson($this->apiUrl('entries'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function index_dapat_difilter_berdasarkan_entry_type(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        ConsumptionEntry::factory()->count(2)->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
        ]);

        ConsumptionEntry::factory()->count(1)->create([
            'user_id'    => $user->id,
            'entry_type' => 'public_transit',
        ]);

        $response = $this->getJson(
            $this->apiUrl('entries') . '?entry_type=food',
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function index_dapat_difilter_berdasarkan_start_date_dan_end_date(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        ConsumptionEntry::factory()->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
            'entry_date'   => '2026-01-15',
        ]);
        ConsumptionEntry::factory()->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
            'entry_date'   => '2026-02-15',
        ]);

        $response = $this->getJson(
            $this->apiUrl('entries') . '?start_date=2026-01-01&end_date=2026-01-31',
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function index_mengembalikan_array_kosong_jika_tidak_ada_entri(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson($this->apiUrl('entries'), $headers);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function index_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('entries'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | SHOW — Detail satu entri
     * ===================================================================== */

    /** @test */
    public function show_mengembalikan_detail_entri(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        $entry = ConsumptionEntry::factory()->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
        ]);

        $response = $this->getJson(
            $this->apiUrl("entries/{$entry->id}"),
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.id', $entry->id)
                 ->assertJsonPath('data.entry_type', 'food');
    }

    /** @test */
    public function show_mengembalikan_404_jika_entri_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->getJson(
            $this->apiUrl('entries/99999'),
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function show_gagal_tanpa_autentikasi(): void
    {
        $response = $this->getJson(
            $this->apiUrl('entries/1'),
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    /* =====================================================================
     | DESTROY — Hapus entri
     * ===================================================================== */

    /** @test */
    public function destroy_berhasil_menghapus_entri(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->makeAuthUser();

        $foodItem = FoodItem::factory()->create();

        $entry = ConsumptionEntry::factory()->create([
            'user_id'      => $user->id,
            'entry_type'   => 'food',
            'food_item_id' => $foodItem->id,
        ]);

        $response = $this->deleteJson(
            $this->apiUrl("entries/{$entry->id}"),
            [],
            $headers
        );

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('consumption_entries', ['id' => $entry->id]);
    }

    /** @test */
    public function destroy_mengembalikan_404_jika_entri_tidak_ada(): void
    {
        ['headers' => $headers] = $this->makeAuthUser();

        $response = $this->deleteJson(
            $this->apiUrl('entries/99999'),
            [],
            $headers
        );

        $response->assertStatus(404)
                 ->assertJsonPath('success', false);
    }

    /** @test */
    public function destroy_gagal_tanpa_autentikasi(): void
    {
        $response = $this->deleteJson(
            $this->apiUrl('entries/1'),
            [],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }
}
