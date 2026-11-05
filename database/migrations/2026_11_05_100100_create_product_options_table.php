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
        Schema::create('product_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_type_id')->constrained()->cascadeOnDelete();
            $table->string('title')->comment('Название значения: Красный, Коробка, 8 марта');
            $table->string('slug')->comment('Транслитерация названия');
            $table->string('image')->nullable()->comment('Изображение значения');
            $table->string('external_id')->nullable()->comment('Внешний идентификатор, например из МойСклад');
            $table->json('meta')->nullable()->comment('Дополнительные данные: hex, price_delta и т.д.');
            $table->unsignedSmallInteger('position')->default(0)->comment('Порядок вывода в фильтре');
            $table->boolean('is_active')->default(true)->comment('Показывать значение в фильтре');
            $table->timestamps();

            $table->unique(['product_option_type_id', 'slug']);
            $table->index(['product_option_type_id', 'position']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
