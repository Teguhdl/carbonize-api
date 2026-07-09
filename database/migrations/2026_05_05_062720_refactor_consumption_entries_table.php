<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        Schema::table('consumption_entries', function (Blueprint $table) use ($isSqlite) {
            // Discriminator — menentukan tipe kalkulasi
            $table->enum('entry_type', ['food', 'private_vehicle', 'public_transit'])
                  ->after('user_id')
                  ->comment('Tipe entri: food, private_vehicle, atau public_transit');

            // FK untuk food
            $table->unsignedBigInteger('food_item_id')->nullable()->after('entry_type');

            // FK untuk private vehicle
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->after('food_item_id');

            $table->unsignedBigInteger('fuel_type_id')->nullable()->after('vehicle_type_id');

            $table->decimal('custom_efficiency', 10, 4)->nullable()->after('fuel_type_id')
                  ->comment('Override efisiensi kendaraan (km/liter). NULL = pakai default kendaraan');

            // FK untuk public transit
            $table->unsignedBigInteger('transit_vehicle_id')->nullable()->after('custom_efficiency');

            // SQLite tidak mendukung penambahan foreign key pada tabel yang sudah ada.
            // Constraint FK hanya dibuat pada database production (MySQL).
            if (! $isSqlite) {
                $table->foreign('food_item_id')->references('id')->on('food_items')->onDelete('set null');
                $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('set null');
                $table->foreign('fuel_type_id')->references('id')->on('fuel_types')->onDelete('set null');
                $table->foreign('transit_vehicle_id')->references('id')->on('transit_vehicles')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consumption_entries', function (Blueprint $table) {
            $table->dropForeign(['food_item_id']);
            $table->dropForeign(['vehicle_type_id']);
            $table->dropForeign(['fuel_type_id']);
            $table->dropForeign(['transit_vehicle_id']);
            $table->dropColumn([
                'entry_type',
                'food_item_id',
                'vehicle_type_id',
                'fuel_type_id',
                'custom_efficiency',
                'transit_vehicle_id',
            ]);
        });
    }
};
