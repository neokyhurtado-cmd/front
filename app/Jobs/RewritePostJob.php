<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Str;

class RewritePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $postId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = Post::find($this->postId);
        if (!$post || $post->body) return;

        $prompt = <<<PROMPT
Reescribe y amplía en español colombiano, con tono informativo y educativo (SEO),
sobre movilidad/señalización. Incluye subtítulos H2/H3 y lista con rutas/medidas si aplica.
No inventes datos; si falta detalle, recomienda consultar la fuente.
Usa formato HTML para los subtítulos y listas.
Título: "{$post->title}"
Resumen: "{$post->excerpt}"
PROMPT;

        try {
            $res = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role'=>'system','content'=>'Eres redactor experto en movilidad urbana y señalización vial para Colombia.'],
                    ['role'=>'user','content'=>$prompt],
                ],
                'temperature'=>0.5,
            ]);

            $body = trim($res->choices[0]->message->content ?? '');
            if (!$body) return;

            // Meta description
            $metaPrompt = "Resume en 150-160 caracteres, atractivo y con palabra clave de movilidad: {$post->title}";
            $meta = OpenAI::chat()->create([
                'model'=>'gpt-3.5-turbo',
                'messages'=>[['role'=>'user','content'=>$metaPrompt]],
                'temperature'=>0.3,
            ]);
            $desc = trim($meta->choices[0]->message->content ?? '');

            $post->update([
                'body' => $body,
                'meta_title' => Str::limit($post->title.' | Panorama Ingeniería IA', 60, ''),
                'meta_description' => Str::limit($desc, 160, ''),
            ]);

        } catch (\Exception $e) {
            \Log::error("Error rewriting post {$this->postId}: " . $e->getMessage());
            // Retry the job up to 3 times
            if ($this->attempts() < 3) {
                $this->release(60); // Retry after 1 minute
            }
        }
    }
}
