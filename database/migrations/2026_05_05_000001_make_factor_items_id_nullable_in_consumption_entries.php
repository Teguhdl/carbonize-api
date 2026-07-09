<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat factor_items_id nullable karena entri transport domain-baru
     * tidak lagi menggunakan emission_factor_items — melainkan vehicle_type_id,
     * fuel_type_id, atau transit_vehicle_id secara langsung.
     */
    public function up(): void
    {
        // SQLite (digunakan saat testing) tidak mendukung dropForeign.
        // Kolom nullable sudah ditangani oleh migration refactor berikutnya,
        // jadi operasi ini cukup dilewati pada SQLite.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('consumption_entries', function (Blueprint $table) {
            // Drop foreign key dulu sebelum mengubah kolom
            $table->dropForeign(['factor_items_id']);

            // Ubah menjadi nullable
            $table->unsignedBigInteger('factor_items_id')->nullable()->change();

            // Tambahkan kembali foreign key dengan onDelete set null agar tidak
            // menghapus entry jika factor item dihapus
            $table->foreign('factor_items_id')
                ->references('id')
                ->on('emission_factor_items')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('consumption_entries', function (Blueprint $table) {
            $table->dropForeign(['factor_items_id']);

            $table->unsignedBigInteger('factor_items_id')->nullable(false)->change();

            $table->foreign('factor_items_id')
                ->references('id')
                ->on('emission_factor_items')
                ->onDelete('cascade');
        });
    }
};
