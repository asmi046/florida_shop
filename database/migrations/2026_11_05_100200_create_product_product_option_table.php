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
        Schema::create('product_product_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->nullable()->comment('Количество, если применимо к типу');
            $table->unsignedSmallInteger('sort')->default(0)->comment('Порядок вывода у товара');
            $table->timestamps();

            $table->unique(['product_id', 'product_option_id']);
            $table->index(['product_option_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_option');
    }
};
