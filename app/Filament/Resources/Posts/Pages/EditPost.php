<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use Filament\Notifications\Notification;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['pinned'] ?? false) && empty($data['pinned_until'])) {
            $base = $data['published_at'] ?? $data['publish_at'] ?? now();
            $data['pinned_until'] = \Illuminate\Support\Carbon::parse($base)->addDays(30);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Limpiar cache del home después de guardar
        cache()->forget('home:v1:');
        cache()->forget('home:v1:' . request('q', ''));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generarResumenIA')
                ->label('🤖 Generar SEO con IA')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    /** @var Post $post */
                    $post = $this->getRecord();

                    try {
                        $prompt = <<<PROMPT
Eres redactor experto en movilidad urbana y SEO. 
Genera un extracto de 150-160 caracteres para meta descripción, claro y atractivo.
Título: "{$post->title}"
Contenido existente: "{$post->excerpt}".
PROMPT;

                        $res = OpenAI::chat()->create([
                            'model' => 'gpt-4o-mini',
                            'messages' => [
                                ['role'=>'system','content'=>'Escribe en español de Colombia, tono informativo.'],
                                ['role'=>'user','content'=>$prompt],
                            ],
                            'temperature' => 0.4,
                        ]);

                        $desc = trim($res->choices[0]->message->content ?? '');
                        $post->update([
                            'meta_title' => Str::limit(($post->meta_title ?: $post->title).' | Panorama Ingeniería IA', 60, ''),
                            'meta_description' => Str::limit($desc, 160, ''),
                            'excerpt' => $post->excerpt ?: Str::limit(strip_tags($post->body ?? ''), 200),
                        ]);

                        Notification::make()
                            ->title('SEO generado con IA exitosamente')
                            ->success()
                            ->send();

                        // Recargar el formulario
                        $this->fillForm();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al generar con IA')
                            ->body('Verifica tu API key: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('generarTagsIA')
                ->label('🤖 Sugerir tags')
                ->color('info')
                ->requiresConfirmation()
                ->action(function () {
                    $post = $this->getRecord();

                    try {
                        $txt = Str::limit(strip_tags($post->title.' '.$post->excerpt.' '.$post->body), 3000);
                        $prompt = <<<PROMPT
Del siguiente texto sobre movilidad urbana, devuelve entre 4 y 8 etiquetas relevantes (1-2 palabras) en minúsculas, separadas por coma, sin #.
Texto: {$txt}
PROMPT;

                        $res = OpenAI::chat()->create([
                            'model' => 'gpt-4o-mini',
                            'messages' => [
                                ['role'=>'system','content'=>'Responde solo con etiquetas separadas por coma.'],
                                ['role'=>'user','content'=>$prompt],
                            ],
                            'temperature' => 0.3,
                        ]);

                        $line = trim($res->choices[0]->message->content ?? '');
                        $tags = collect(explode(',', $line))
                            ->map(fn($t)=>trim(Str::lower($t)))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();
                        
                        $post->update(['tags'=>$tags]);

                        Notification::make()
                            ->title('Tags sugeridos con IA')
                            ->success()
                            ->send();

                        $this->fillForm();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al generar tags')
                            ->body('Verifica tu API key: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('publicarAhora')
                ->label('📢 Publicar ahora')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $post = $this->getRecord();
                    $post->update([
                        'status' => 'published',
                        'published_at' => now(),
                        'publish_at' => $post->publish_at ?? now(),
                    ]);
                    
                    Notification::make()
                        ->title('Post publicado exitosamente')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
