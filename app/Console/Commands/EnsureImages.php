<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use App\Jobs\EnsurePostImage;

class EnsureImages extends Command
{
    protected $signature = 'images:ensure {--limit=10} {--force}';
    protected $description = 'Asegurar que todos los posts tengan imágenes (OG + IA)';

    public function handle(): int
    {
        $limit = (int)$this->option('limit');
        $force = $this->option('force');
        
        $query = Post::latest();
        
        if (!$force) {
            $query->whereNull('image_path');
        }
        
        $posts = $query->limit($limit)->get();
        
        if ($posts->isEmpty()) {
            $this->info('No hay posts para procesar.');
            return self::SUCCESS;
        }
        
        $this->info("Procesando {$posts->count()} posts...");
        
        $posts->each(function($post) {
            dispatch(new EnsurePostImage($post));
            $this->line("Encolado: {$post->title}");
        });
        
        $this->info('Pipeline completo iniciado: OG → IA → Storage.');
        
        return self::SUCCESS;
    }
}