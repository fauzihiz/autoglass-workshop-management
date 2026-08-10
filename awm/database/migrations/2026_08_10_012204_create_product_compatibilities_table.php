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
        Schema::create('product_compatibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('glass_product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_model_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('year_from')->nullable();
            $table->smallInteger('year_to')->nullable();
            $table->timestamps();

            $table->unique(['glass_product_id', 'car_model_id', 'year_from', 'year_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_compatibilities');
    }
};
