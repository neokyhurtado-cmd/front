<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Hero')->schema([
                    \Filament\Forms\Components\TextInput::make('hero_title')->required()->maxLength(120),
                    \Filament\Forms\Components\Textarea::make('hero_subtitle')->rows(2),
                ]),
                \Filament\Forms\Components\Section::make('Notificación')->schema([
                    \Filament\Forms\Components\Textarea::make('notification_text')->rows(3),
                ]),
                \Filament\Forms\Components\Section::make('Sidebar')->schema([
                    \Filament\Forms\Components\TagsInput::make('sidebar_topics')
                        ->placeholder('Escribe y presiona Enter'),
                    \Filament\Forms\Components\TextInput::make('corporate_url')->url(),
                ]),
                \Filament\Forms\Components\Section::make('Tema')->schema([
                    \Filament\Forms\Components\Toggle::make('dark_default')->label('Iniciar en modo oscuro'),
                ]),
            ])->columns(1);
    }
}
