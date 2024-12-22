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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('full_name')->NotNull();
            $table->string('email')->unique()->NotNull();
            $table->string('password')->NotNull();
            $table->string('address')->nullable();
            $table->string('phone_number')->unique()->NotNull();
            $table->timestamp('email_verified_at')->nullable();
            $table->uuid('role_id')->NotNull();
            $table->uuid('business_id')->nullable();
            $table->foreign('role_id')->references('role_id')->on('roles');
            $table->foreign('business_id')->references('business_id')->on('businesses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
