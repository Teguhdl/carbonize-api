<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('emission_factor', 10, 6)->nullable()->comment('kgCO2e per kg. NULL jika pakai Climatiq');
            $table->string('climatiq_id')->nullable()->comment('Climatiq activity ID');
            $table->enum('calculation_method', ['fixed', 'climatiq'])
                  ->default('fixed')
                  ->comment('fixed = pakai emission_factor lokal; climatiq = hit Climatiq API');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
