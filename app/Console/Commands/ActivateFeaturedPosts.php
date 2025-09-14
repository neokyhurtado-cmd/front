<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class ActivateFeaturedPosts extends Command
{
    protected $signature = 'posts:activate-featured {--count=3 : Number of posts to feature}';
    
    protected $description = 'Activate the latest posts as featured for testing';

    public function handle(): int
    {
        $count = (int) $this->option('count');
        
        $posts = Post::latest()->take($count)->get();
        
        if ($posts->isEmpty()) {
            $this->error('No posts found to activate.');
            return 1;
        }

        foreach ($posts as $post) {
            $post->update([
                'is_pinned' => true,
                'pinned_until' => now()->addDays(30)
            ]);
        }

        $this->info("✅ Activated {$posts->count()} posts as featured:");
        
        foreach ($posts as $post) {
            $this->line("   • {$post->title}");
        }

        $totalFeatured = Post::featured()->count();
        $this->info("📊 Total featured posts: {$totalFeatured}");

        return 0;
    }
}