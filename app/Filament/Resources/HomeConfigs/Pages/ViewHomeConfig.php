<?php

namespace App\Filament\Resources\HomeConfigs\Pages;

use App\Filament\Resources\HomeConfigs\HomeConfigResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHomeConfig extends ViewRecord
{
    protected static string $resource = HomeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
