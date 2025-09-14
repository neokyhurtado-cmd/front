<?php

namespace App\Filament\Resources\HomeConfigs\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HomeConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required(),
                Textarea::make('value')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required()
                    ->default('text'),
                TextInput::make('group')
                    ->required()
                    ->default('general'),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
