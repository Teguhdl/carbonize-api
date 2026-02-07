<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emission_factor_items', function (Blueprint $table) {
            $table->string('climatiq_id')->nullable()->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('emission_factor_items', function (Blueprint $table) {
            $table->dropColumn('climatiq_id');
        });
    }
};
