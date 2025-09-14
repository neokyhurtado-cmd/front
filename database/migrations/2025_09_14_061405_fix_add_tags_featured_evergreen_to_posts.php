<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'tags')) {
            Schema::table('posts', function (Blueprint $t) {
                $t->json('tags')->nullable()->after('meta_description');
            });
        }

        if (! Schema::hasColumn('posts', 'featured_image')) {
            Schema::table('posts', function (Blueprint $t) {
                $t->string('featured_image')->nullable()->after('tags');
            });
        }

        if (! Schema::hasColumn('posts', 'evergreen')) {
            Schema::table('posts', function (Blueprint $t) {
                $t->boolean('evergreen')->default(false)->after('featured_image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('posts', 'evergreen')) {
            Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('evergreen'));
        }
        if (Schema::hasColumn('posts', 'featured_image')) {
            Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('featured_image'));
        }
        if (Schema::hasColumn('posts', 'tags')) {
            Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('tags'));
        }
    }
};
