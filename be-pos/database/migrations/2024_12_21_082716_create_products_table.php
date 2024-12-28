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
            $table->boolean('status');
            $table->decimal('price', 15, 2)->NotNull();
            $table->enum('gender', ['male', 'female', 'unisex']);
            $table->text('description')->nullable();
            $table->json('photo_product')->nullable();
            $table->integer('stock')->NotNull()->check('stock >= 0');
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
