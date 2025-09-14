<?php

namespace App\Filament\Resources\HomeConfigs\Pages;

use App\Filament\Resources\HomeConfigs\HomeConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeConfigs extends ListRecords
{
    protected static string $resource = HomeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
