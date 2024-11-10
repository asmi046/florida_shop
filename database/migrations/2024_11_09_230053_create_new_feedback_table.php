<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_feedback', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->comment('Имя оставившего отзыв');
            $table->date('data')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Дата публикации');
            $table->string('platform')->comment('Платформа');
            $table->string('img', 500)->nullable()->comment('Изображение');
            $table->integer('score')->comment('Оценка');
            $table->text('description')->nullable()->comment('Платформа');
            $table->string('platform_lnk')->nullable()->comment('Платформа');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_feedback');
    }
};
