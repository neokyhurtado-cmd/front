<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\AiEditorialService;
use Illuminate\Console\Command;

class ProcessEditorialAi extends Command
{
    protected $signature = 'posts:editorial-ai {--featured-only : Process only featured posts} {--limit=10 : Limit number of posts}';
    
    protected $description = 'Process posts with AI editorial service (titles, excerpts, keywords)';

    public function handle(): int
    {
        $query = Post::query();
        
        if ($this->option('featured-only')) {
            $query->featured();
        }
        
        $posts = $query->take($this->option('limit'))->get();
        
        if ($posts->isEmpty()) {
            $this->error('No posts found to process.');
            return 1;
        }

        $ai = app(AiEditorialService::class);
        $processed = 0;
        $errors = 0;

        $this->info("🤖 Processing {$posts->count()} posts with AI Editorial Service...");
        
        foreach ($posts as $post) {
            try {
                $this->line("Processing: {$post->title}");
                
                [$title, $excerpt, $keywords] = $ai->makeEditorial(
                    $post->raw_text ?? ($post->content ?? $post->body ?? $post->title)
                );

                $post->update([
                    'excerpt' => $excerpt,
                    'seo_keywords' => $keywords,
                    // No sobreescribir título si ya existe
                ]);

                $processed++;
                $this->line("   ✅ Updated");
                
            } catch (\Throwable $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->line("   Processed: {$processed}");
        $this->line("   Errors: {$errors}");

        return $errors > 0 ? 1 : 0;
    }
}