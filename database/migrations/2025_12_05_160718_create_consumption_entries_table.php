<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumption_entries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('factor_items_id');

            $table->dateTime('entry_date');
            $table->decimal('emissions', 12, 4);
            $table->string('image')->nullable();

            // Metadata in JSON format
            $table->json('metadata')->nullable();

            $table->decimal('quantity', 12, 4);
            $table->timestamps(); // includes created_at & updated_at

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('factor_items_id')
                ->references('id')
                ->on('emission_factor_items')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_entries');
    }
};
