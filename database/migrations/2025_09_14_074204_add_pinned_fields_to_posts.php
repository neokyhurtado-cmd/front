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
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts','pinned')) $table->boolean('pinned')->default(false)->after('status')->index();
            if (!Schema::hasColumn('posts','pinned_until')) $table->dateTime('pinned_until')->nullable()->after('pinned')->index();
            if (!Schema::hasColumn('posts','published_at')) $table->dateTime('published_at')->nullable()->after('publish_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts','pinned_until')) $table->dropColumn('pinned_until');
            if (Schema::hasColumn('posts','pinned')) $table->dropColumn('pinned');
            // No eliminamos published_at por si se usa en otros lados
        });
    }
};
