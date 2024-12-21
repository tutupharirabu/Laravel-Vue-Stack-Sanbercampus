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
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->uuid('invoice_detail_id')->primary();
            $table->integer('quantity')->NotNull()->Check('quantity > 0');
            $table->decimal('price', 15, 2)->NotNull()->Check('price > 0');
            $table->decimal('line_total', 15, 2)->NotNull()->Check('line_total > 0');
            $table->uuid('invoice_id')->NotNull();
            $table->uuid('product_id')->NotNull();
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice__details');
    }
};
