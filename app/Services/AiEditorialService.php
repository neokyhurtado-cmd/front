<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiEditorialService
{
    public function makeEditorial(string $raw): array
    {
        $key = config('services.openai.key');
        if (!$key) {
            return [
                $this->limit($raw, 70), 
                $this->limit($raw, 160), 
                'general, noticias'
            ];
        }

        $prompt = <<<TXT
Eres editor de un periódico serio. Reescribe título y extracto estilo MSN/El Tiempo.

Instrucciones de estilo:
- TÍTULO: 55–70 caracteres, verbo fuerte, sin mayúsculas excesivas, sin clickbait.
- EXTRACTO: 140–180 caracteres, objetivo y claro; incluye el qué + dónde; sin opiniones.
- SEO: añade 3–5 keywords separadas por coma, todas en minúscula.
- Idioma: español neutro.
- Formato: JSON con { "title", "excerpt", "keywords" }.

Texto fuente (puede tener ruido):
<<<
{$raw}
>>>
Responde SOLO con el JSON.
TXT;

        try {
            $response = Http::withToken($key)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres editor de noticias profesional.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 300
                ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API error: ' . $response->status());
            }

            $content = data_get($response->json(), 'choices.0.message.content', '');
            
            // Limpiar el contenido para extraer solo el JSON
            $content = trim($content);
            if (str_starts_with($content, '```json')) {
                $content = trim(str_replace(['```json', '```'], '', $content));
            }

            $json = json_decode($content, true);
            
            if (!is_array($json)) {
                throw new \Exception('Invalid JSON response');
            }

            return [
                (string)($json['title'] ?? $this->limit($raw, 70)),
                (string)($json['excerpt'] ?? $this->limit($raw, 160)),
                (string)($json['keywords'] ?? 'general, noticias'),
            ];

        } catch (\Throwable $e) {
            \Log::warning('AiEditorialService failed: ' . $e->getMessage());
            
            // Fallback: usar el texto original procesado
            return [
                $this->limit($raw, 70),
                $this->limit($raw, 160),
                'general, noticias'
            ];
        }
    }

    private function limit(string $text, int $maxLength): string
    {
        // Limpiar el texto
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
        
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Cortar en palabra completa si es posible
        $truncated = mb_substr($text, 0, $maxLength - 1);
        $lastSpace = mb_strrpos($truncated, ' ');
        
        if ($lastSpace !== false && $lastSpace > $maxLength * 0.8) {
            return mb_substr($truncated, 0, $lastSpace) . '…';
        }
        
        return $truncated . '…';
    }

    public function generateImagePrompt(string $title): string
    {
        return "Foto editorial sobria, estilo portada de periódico, relación 16:9. " .
               "Tema: \"{$title}\". " .
               "Colores neutros, alto contraste, sin texto sobre la imagen, sin logos. " .
               "Composición clara, foco en el hecho (infraestructura, transporte urbano, señalización).";
    }
}