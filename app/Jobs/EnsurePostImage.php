<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnsurePostImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        // 1) Intenta og:image primero
        dispatch_sync(new BackfillPostImage($this->post));
        
        // 2) Si sigue faltando, genera con IA
        if (!$this->post->fresh()->image_path) {
            dispatch(new GenerateAiImage($this->post));
        }
    }
}