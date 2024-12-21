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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('product_variant_id')->primary();
            $table->string('photo_variant')->nullable();
            $table->string('color')->NotNull();
            $table->integer('stock')->NotNull()->Check('stock >= 0');
            $table->string('sku')->unique()->NotNull();
            $table->boolean('status')->default(false);
            $table->uuid('product_id')->NotNull();
            $table->uuid('size_id')->NotNull();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
            $table->foreign('size_id')->references('size_id')->on('sizes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product__variants');
    }
};
