<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('posts', function (Blueprint $t) {
            $t->boolean('is_pinned')->default(false)->index()->after('status');   // fijado manual
            $t->unsignedTinyInteger('pin_priority')->default(0)->after('is_pinned'); // 0=auto, 1..9=manual
            $t->timestamp('pinned_at')->nullable()->after('pin_priority');
        });
    }
    public function down(): void {
        Schema::table('posts', function (Blueprint $t) {
            $t->dropColumn(['is_pinned','pin_priority','pinned_at']);
        });
    }
};
