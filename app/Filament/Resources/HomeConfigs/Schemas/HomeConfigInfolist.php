<?php

namespace App\Filament\Resources\HomeConfigs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HomeConfigInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('key'),
                TextEntry::make('value')
                    ->columnSpanFull(),
                TextEntry::make('type'),
                TextEntry::make('group'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
