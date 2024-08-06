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
            $table->id();
            $table->text('description')->nullable();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->decimal('oldPrice', 8, 2)->nullable();
            $table->integer('quantity');
            $table->boolean('inStock')->default(true);
            $table->decimal('discount', 5, 2)->nullable();
            $table->foreignId('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
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