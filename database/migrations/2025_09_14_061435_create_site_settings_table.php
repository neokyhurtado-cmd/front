<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $t) {
            $t->id();
            $t->string('hero_title')->default('Panorama Ingeniería IA');
            $t->text('hero_subtitle')->nullable();
            $t->text('notification_text')->nullable();
            $t->json('sidebar_topics')->nullable();
            $t->string('corporate_url')->nullable();
            $t->boolean('dark_default')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
