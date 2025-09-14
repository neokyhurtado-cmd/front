# Prompts IA Editorial - Estilo MSN/El Tiempo

## 📝 Prompt para Títulos + Extractos Editorial

**Uso:** Pasarlo a LLM de texto (GPT-4, Claude, etc.) para reescribir contenido RSS

```
Eres editor de un periódico serio. Reescribe título y extracto a estilo MSN/El Tiempo.

Instrucciones de estilo:
- TÍTULO: 55–70 caracteres, verbo fuerte, sin mayúsculas excesivas, sin clickbait.
- EXTRACTO: 140–180 caracteres, objetivo y claro; incluye el qué + dónde; sin opiniones.
- SEO: añade 3–5 keywords separadas por coma, todas en minúscula.
- Idioma: español neutro.
- Formato: JSON con { "title", "excerpt", "keywords" }.

Texto fuente (puede tener ruido):
<<<
{{ $post->raw_text ?? $post->title }}
>>>
Responde SOLO con el JSON.
```

**Ejemplo de salida esperada:**
```json
{
  "title": "Cierre parcial en la calle 100 por obras de refuerzo",
  "excerpt": "La Secretaría de Movilidad informó cierres parciales en la calle 100 con carrera 15 por obras de refuerzo. Consulte desvíos y horarios autorizados.",
  "keywords": "bogotá, movilidad, cierres viales, obras, desvíos"
}
```

## 🖼️ Prompt para Generación de Imágenes Editorial

**Uso:** Para OpenAI Images API (DALL-E 3) cuando no hay og:image disponible

```
Foto editorial sobria, estilo portada de periódico, relación 16:9.
Tema: "{{ $post->title }}".
Colores neutros, alto contraste, sin texto sobre la imagen, sin logos.
Composición clara, foco en el hecho (infraestructura, transporte urbano, señalización).
```

**Configuración técnica:**
- Tamaño recomendado: `1280x720` o `1024x576`
- Modelo: `dall-e-3`
- Quality: `standard`
- Style: `natural`

## 🔄 Pipeline de Automatización

### Sequence de Jobs:
1. **BackfillPostImage**: Intenta extraer `og:image` de `source_url`
2. **GenerateAiImage**: Si no hay imagen, genera con OpenAI usando el prompt de arriba
3. **EnsurePostImage**: Orchestrador que ejecuta el pipeline completo

### Observer para Posts (opcional):

```php
// app/Observers/PostObserver.php
<?php

namespace App\Observers;

use App\Models\Post;
use App\Jobs\EnsurePostImage;
use App\Services\AiEditorialService;

class PostObserver
{
    public function created(Post $post)
    {
        $this->processPost($post);
    }

    public function updated(Post $post)
    {
        // Solo procesar si cambió a pinned o se publicó
        if ($post->wasChanged(['pinned', 'status'])) {
            $this->processPost($post);
        }
    }

    private function processPost(Post $post)
    {
        // Pipeline de imagen
        dispatch(new EnsurePostImage($post));
        
        // Si tienes servicio de IA para texto:
        if (app()->bound(AiEditorialService::class)) {
            $ai = app(AiEditorialService::class);
            [$title, $excerpt, $keywords] = $ai->makeEditorial($post->raw_text ?? $post->title);
            
            $post->update([
                'title' => $title,
                'excerpt' => $excerpt,
                'seo_keywords' => $keywords
            ]);
        }
    }
}
```

### Registro del Observer:

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    \App\Models\Post::observe(\App\Observers\PostObserver::class);
}
```

## 🎯 Comandos Artisan de Mantenimiento

```bash
# Procesar imágenes faltantes en lote
php artisan posts:backfill-images

# Asegurar que todos los posts tienen imagen
php artisan posts:ensure-images

# Regenerar títulos/extractos con IA (si tienes el servicio)
php artisan posts:editorial-ai --pinned-only
```

## 📊 Configuración en Filament Admin

### Toggle para posts destacados:

```php
// app/Filament/Resources/PostResource.php
Forms\Components\Toggle::make('pinned')
    ->label('Destacar en portada')
    ->helperText('Aparecerá en la fila horizontal superior'),

Forms\Components\DateTimePicker::make('pinned_until')
    ->label('Destacar hasta')
    ->nullable()
    ->helperText('Dejar vacío para destacar indefinidamente'),
```

### Acción en tabla para destacar/quitar:

```php
Tables\Actions\Action::make('toggle_pinned')
    ->label(fn (Post $record) => $record->pinned ? 'Quitar destaque' : 'Destacar')
    ->icon(fn (Post $record) => $record->pinned ? 'heroicon-o-star' : 'heroicon-o-star-outline')
    ->action(function (Post $record) {
        if ($record->pinned) {
            $record->unpin();
        } else {
            $record->pin(30); // 30 días por defecto
        }
    }),
```

## 🌐 Integración con APIs de IA

### Servicio para texto editorial (ejemplo):

```php
// app/Services/AiEditorialService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiEditorialService
{
    public function makeEditorial(string $rawText): array
    {
        $prompt = "Eres editor de un periódico serio. Reescribe título y extracto a estilo MSN/El Tiempo...";
        
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => "<<<\n{$rawText}\n>>>"]
                ],
                'temperature' => 0.3,
                'max_tokens' => 300
            ]);

        $content = $response->json()['choices'][0]['message']['content'] ?? '{}';
        $data = json_decode($content, true) ?: [];

        return [
            $data['title'] ?? 'Título no disponible',
            $data['excerpt'] ?? 'Extracto no disponible',
            $data['keywords'] ?? 'general, noticias'
        ];
    }
}
```

---

## ✅ Checklist Final

- [x] **CSS horizontal**: `.news-featured`, `.news-h` con grid 16:9
- [x] **Partial horizontal**: `news-card-horizontal.blade.php` 
- [x] **Home actualizado**: Fila destacadas + grid 2×3
- [x] **Campo is_pinned**: Confirmado en modelo Post
- [x] **Safelist**: Clases protegidas contra purging
- [x] **Prompts IA**: Documentados y listos para uso

**Para activar completamente:**
1. Marca algunos posts como `pinned=true` en Filament
2. Los prompts de IA están listos para integrar con tu servicio preferido
3. El pipeline de imágenes ya funciona (BackfillPostImage + GenerateAiImage)

¡El layout MSN/El Tiempo con fila destacadas horizontal está listo! 🎯