<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profileImage')->nullable();
            $table->integer('dailyCarbonLimit')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->dateTime('lastLogin')->nullable();
            $table->timestamps(); // includes created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
