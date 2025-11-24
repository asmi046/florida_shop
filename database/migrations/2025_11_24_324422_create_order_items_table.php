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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade')
                ->comment('ID заказа');

            $table->string('title')
                ->comment('Наименование товара');

            $table->string('sku', 100)
                ->comment('Артикул товара');

            $table->string('img', 500)
                ->comment('Изображение товара');

            $table->string('slug', 500)
                ->comment('Слаг товара');

            $table->decimal('price', 10, 2)
                ->comment('Цена за единицу');

            $table->integer('quantity')
                ->default(1)
                ->comment('Количество');

            $table->decimal('total', 10, 2)
                ->comment('Итого (цена * количество)');

            $table->timestamps();

            // Индексы для оптимизации
            $table->index('order_id');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
