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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('status');
            $table->index('type');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('type');
            $table->index('created_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('paid_at');
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['paid_at']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
