<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transit_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Contoh: City Bus, MRT, Minibus/Angkot');
            $table->decimal('emission_factor', 10, 6)->comment('kgCO2e per km per kendaraan (total)');
            $table->decimal('avg_passengers', 10, 2)->comment('Rata-rata jumlah penumpang per perjalanan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transit_vehicles');
    }
};
