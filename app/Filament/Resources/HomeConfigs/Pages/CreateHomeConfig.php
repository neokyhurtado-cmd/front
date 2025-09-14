<?php

namespace App\Filament\Resources\HomeConfigs\Pages;

use App\Filament\Resources\HomeConfigs\HomeConfigResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeConfig extends CreateRecord
{
    protected static string $resource = HomeConfigResource::class;
}
