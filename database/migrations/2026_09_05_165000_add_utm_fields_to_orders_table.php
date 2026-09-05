<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('utm_source')->nullable()->comment('utm_source');
            $table->string('utm_medium')->nullable()->comment('utm_medium');
            $table->string('utm_campaign')->nullable()->comment('utm_campaign');
            $table->string('utm_term')->nullable()->comment('utm_term');
            $table->string('utm_content')->nullable()->comment('utm_content');
            $table->string('utm_referrer', 1024)->nullable()->comment('utm_referrer');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'utm_referrer',
            ]);
        });
    }
};
