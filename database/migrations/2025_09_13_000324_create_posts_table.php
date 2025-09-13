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
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->enum('type', ['news','educational'])->default('news');
            $t->enum('status', ['draft','scheduled','published','archived'])->default('draft');
            $t->string('source')->nullable();         // nombre de fuente
            $t->string('source_url')->nullable();     // url original
            $t->string('image_url')->nullable();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();         // texto final (IA)
            $t->json('tags')->nullable();
            $t->timestamp('fetched_at')->nullable();  // cuándo llegó del RSS
            $t->timestamp('publish_at')->nullable();  // cuándo se publicará
            $t->timestamp('published_at')->nullable();
            $t->boolean('evergreen')->default(false); // educativo = duradero
            // SEO
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->string('canonical_url')->nullable();
            $t->timestamps();
            $t->index(['status','publish_at']);
            $t->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
