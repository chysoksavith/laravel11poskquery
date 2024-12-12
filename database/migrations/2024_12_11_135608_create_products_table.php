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
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Corrected 'cascde' to 'cascade'
            $table->foreignId('brand_id')->constrained()->onDelete('cascade'); // Added 'cascade' onDelete for brand_id
            $table->string('product_code');
            $table->string('product_name');
            $table->decimal('product_price', 8, 2);
            $table->decimal('selling_price', 8, 2);
            $table->string('discount');
            $table->string('stock');
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
