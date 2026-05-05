<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Nama bahan bakar, contoh: Pertalite, Pertamax');
            $table->decimal('emission_factor', 10, 6)->comment('kgCO2e per liter');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_types');
    }
};
