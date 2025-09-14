<?php

namespace App\Filament\Resources\HomeConfigs\Pages;

use App\Filament\Resources\HomeConfigs\HomeConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeConfig extends EditRecord
{
    protected static string $resource = HomeConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
