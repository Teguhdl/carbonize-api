<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emission_factor_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('factor_category_id');
            $table->string('name');
            $table->decimal('value', 10, 3);
            $table->timestamps();

            $table->foreign('factor_category_id')
                ->references('id')
                ->on('emission_factor_categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emission_factor_items');
    }
};
