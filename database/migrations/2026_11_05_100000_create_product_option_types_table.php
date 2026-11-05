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
        Schema::create('product_option_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('Идентификатор типа: color, packing, audience, event, composition');
            $table->string('title')->comment('Подпись типа в фильтре');
            $table->unsignedSmallInteger('position')->default(0)->comment('Порядок вывода в фильтре');
            $table->boolean('show_in_filter')->default(true)->comment('Показывать тип в фильтре каталога');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_types');
    }
};
