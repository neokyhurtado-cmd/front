<?php

namespace App\Observers;

use App\Models\Post;
use App\Jobs\EnsurePostImage;
use App\Services\AiEditorialService;

class PostObserver
{
    public function saved(Post $post): void
    {
        // 1) Pipeline de imagen (og:image primero; si no, IA)
        dispatch(new EnsurePostImage($post));

        // 2) Título/Extracto editoriales si está "pinned" o faltan campos
        if ($this->shouldProcessEditorial($post)) {
            try {
                $ai = app(AiEditorialService::class);
                [$title, $excerpt, $keywords] = $ai->makeEditorial(
                    $post->raw_text ?? ($post->content ?? $post->body ?? $post->title)
                );

                // No sobreescribir si ya lo editaste a mano
                $updates = [];
                if (blank($post->title) || $post->title === $post->getOriginal('title')) {
                    $updates['title'] = $title;
                }
                if (blank($post->excerpt)) {
                    $updates['excerpt'] = $excerpt;
                }
                if (blank($post->seo_keywords)) {
                    $updates['seo_keywords'] = $keywords;
                }

                if (!empty($updates)) {
                    $post->updateQuietly($updates);
                }
            } catch (\Throwable $e) {
                // Silencioso: no rompe el guardado
                \Log::warning('AiEditorialService error: ' . $e->getMessage());
            }
        }
    }

    private function shouldProcessEditorial(Post $post): bool
    {
        // Procesar si está destacado o faltan campos importantes
        return ($post->pinned || $post->is_pinned) || 
               blank($post->excerpt) || 
               blank($post->seo_keywords);
    }
}