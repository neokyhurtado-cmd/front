<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use App\Jobs\BackfillPostImage;

class BackfillImages extends Command
{
    protected $signature = 'images:backfill {--limit=50}';
    protected $description = 'Intentar completar imágenes desde og:image';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        
        $posts = Post::whereNull('image_path')->latest()->limit($limit)->get();
        
        $this->info("Procesando {$posts->count()} posts sin imágenes...");
        
        $posts->each(function($post) {
            dispatch(new BackfillPostImage($post));
            $this->line("Encolado: {$post->title}");
        });
        
        $this->info('Todas las tareas han sido encoladas.');
        
        return self::SUCCESS;
    }
}