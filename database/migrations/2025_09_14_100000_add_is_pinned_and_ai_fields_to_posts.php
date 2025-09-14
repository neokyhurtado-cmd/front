<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Solo añadir campos que no existen
            if (!Schema::hasColumn('posts', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->index();
            }
            
            // Campos para atribución de imágenes
            if (!Schema::hasColumn('posts', 'image_source_label')) {
                $table->string('image_source_label')->nullable();
            }
            if (!Schema::hasColumn('posts', 'image_source_url')) {
                $table->string('image_source_url')->nullable();
            }
            
            // Campos para IA editorial
            if (!Schema::hasColumn('posts', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable();
            }
            if (!Schema::hasColumn('posts', 'raw_text')) {
                $table->text('raw_text')->nullable();
            }
            if (!Schema::hasColumn('posts', 'content')) {
                $table->text('content')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'is_pinned', 
                'image_source_label', 
                'image_source_url',
                'seo_keywords',
                'raw_text',
                'content'
            ]);
        });
    }
};