<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('confirm_address_with_recipient')->default(false)->comment('Уточнить адрес доставки у получателя');
            $table->string('recipient_type')->default('Другой человек')->comment('Кто получатель');
            $table->boolean('anonymous_delivery')->default(false)->comment('Доставить анонимно');
            $table->boolean('deliver_asap')->default(false)->comment('Доставить в ближайшее время');
            $table->boolean('agree_delivery_time_with_recipient')->default(false)->comment('Согласовать время доставки с получателем');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'confirm_address_with_recipient',
                'recipient_type',
                'anonymous_delivery',
                'deliver_asap',
                'agree_delivery_time_with_recipient',
            ]);
        });
    }
};
