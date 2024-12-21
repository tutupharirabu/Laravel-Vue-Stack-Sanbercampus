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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('product_id')->primary();
            $table->string('product_name')->NotNull();
            $table->string('sku')->unique()->NotNull();
            $table->boolean('status')->default(false);
            $table->decimal('price', 15, 2)->NotNiull();
            $table->enum('gender', ['male', 'female', 'unisex']);
            $table->text('description')->nullable();
            $table->string('photo_product')->nullable();
            $table->uuid('category_id')->NotNull();
            $table->foreign('category_id')->references('category_id')->on('categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
