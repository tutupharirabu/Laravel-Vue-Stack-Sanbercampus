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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('invoice_id')->primary();
            $table->string('snap_token')->unique()->nullable();
            $table->string('status')->default('unpaid');
            $table->dateTime('due_date')->NotNull();
            $table->decimal('total_amount', 15, 2)->NotNull();
            $table->uuid('user_id')->NotNull();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
