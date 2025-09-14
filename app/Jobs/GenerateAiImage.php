<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAiImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        if ($this->post->image_path) return;

        $prompt = "Foto editorial minimalista sobre: {$this->post->title}. "
                . "Estilo periódico, colores sobrios, alta legibilidad, 16:9.";

        $apiKey = config('services.openai.key');
        if (!$apiKey) return;

        try {
            // OpenAI Images: dall-e-3 para mejor calidad
            $res = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                ])->post('https://api.openai.com/v1/images/generations', [
                    'model'  => 'dall-e-3',
                    'prompt' => $prompt,
                    'size'   => '1024x1024',
                    'quality' => 'standard',
                    'response_format' => 'b64_json'
                ]);

            if (!$res->ok()) return;

            $data = $res->json();
            $b64  = $data['data'][0]['b64_json'] ?? null;
            if (!$b64) return;

            $bin = base64_decode($b64);
            $path = "posts/ai-{$this->post->id}.png";
            Storage::disk('public')->put($path, $bin);
            $this->post->image_path = $path;
            $this->post->save();
        } catch (\Throwable $e) {
            // silenciar: si falla OpenAI, usamos placeholder
        }
    }
}