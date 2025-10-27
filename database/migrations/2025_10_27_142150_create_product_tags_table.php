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
        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Заголовок тега');
            $table->string('alt_title')->comment('Альтернативный заголовок тега');
            $table->string('template')->nullable()->comment('Шаблон для отображения');
            $table->string('image', 800)->nullable()->comment('Путь к изображению тега');
            $table->string('slug')->unique()->comment('Уникальный слаг для URL');
            $table->text('description')->nullable()->comment('Описание тега');
            $table->string('seo_title')->nullable()->comment('SEO заголовок');
            $table->string('seo_description')->nullable()->comment('SEO описание');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tags');
    }
};
