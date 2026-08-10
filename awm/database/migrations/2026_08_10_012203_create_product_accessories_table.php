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
        Schema::create('product_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glass_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accessory_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['glass_product_id', 'accessory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_accessories');
    }
};
