<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackfillPostImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        if ($this->post->image_path || !$this->post->source_url) return;

        try {
            $resp = Http::timeout(10)->get($this->post->source_url);
            if (!$resp->ok()) return;

            // Buscar og:image
            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)/i', $resp->body(), $m)) {
                $url = html_entity_decode($m[1]);
                $img = Http::timeout(10)->get($url);
                if ($img->ok()) {
                    $ext = Str::of(parse_url($url, PHP_URL_PATH))->afterLast('.')->lower() ?: 'jpg';
                    $path = "posts/{$this->post->id}.".$ext;
                    Storage::disk('public')->put($path, $img->body());
                    $this->post->image_path = $path;
                    $this->post->save();
                }
            }
        } catch (\Throwable $e) {
            // silenciar: si falla, seguimos con niveles B/C
        }
    }
}