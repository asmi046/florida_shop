<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('short_description', 255)->nullable()->after('title');
            $table->timestamp('published_at')->nullable()->after('created_at');
        });

        DB::table('blog_posts')
            ->whereNull('short_description')
            ->update([
                'short_description' => DB::raw('title'),
                'published_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]);

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('short_description', 255)->nullable(false)->change();
            $table->timestamp('published_at')->useCurrent()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'published_at']);
        });
    }
};
