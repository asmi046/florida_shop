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
        Schema::create('my_sklad_assortiments', function (Blueprint $table) {
            $table->id();
            $table->uuid('sklad_id')->unique();
            $table->string('type');
            $table->string('name', 500);
            $table->string('code')->nullable();
            $table->string('externalCode')->nullable();
            $table->string('pathName')->nullable();
            $table->string('components_href', 700)->nullable();
            $table->integer('components_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_sklad_assortiments');
    }
};
