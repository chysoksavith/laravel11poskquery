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
        Schema::create('Supplier', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_telephone')->nullable();
            $table->text('supplier_address')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Supplier');
    }
};
