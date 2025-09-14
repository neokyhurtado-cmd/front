<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['pinned'] ?? false) && empty($data['pinned_until'])) {
            $base = $data['published_at'] ?? $data['publish_at'] ?? now();
            $data['pinned_until'] = \Illuminate\Support\Carbon::parse($base)->addDays(30);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Limpiar cache del home después de crear
        cache()->forget('home:v1:');
        cache()->forget('home:v1:' . request('q', ''));
    }
}
