<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->uuid('business_id')->primary();
            $table->string('business_name')->unique()->NotNull();
            $table->string('city')->NotNull();
            $table->string('district')->NotNull();
            $table->string('postal_code')->NotNull();
            $table->string('photo_id_card')->NotNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bussinesses');
    }
};
