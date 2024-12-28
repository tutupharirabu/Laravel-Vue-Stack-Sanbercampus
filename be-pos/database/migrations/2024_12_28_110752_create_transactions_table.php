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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('transaction_id')->primary();
            $table->uuid('customer_id')->NotNull();
            $table->uuid('cashier_id')->NotNull();
            $table->foreign('customer_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->foreign('cashier_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->boolean('status');
            $table->decimal('amount', 15, 2)->NotNull();
            $table->json('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
