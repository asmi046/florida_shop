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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('polname')->nullable();
            $table->string('polphone', 50)->nullable();
            $table->string('delivery', 500)->nullable();
            $table->string('podezd', 5)->nullable();
            $table->string('etazg', 5)->nullable();
            $table->string('kvartira', 5)->nullable();
            $table->string('data', 50)->nullable();
            $table->string('time', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'polname',
                'polphone',
                'delivery',
                'podezd',
                'etazg',
                'kvartira',
                'data',
                'time',
            ]);
        });
    }
};
