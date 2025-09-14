<?php

namespace App\Filament\Resources\HomeConfigs;

use App\Filament\Resources\HomeConfigs\Pages\CreateHomeConfig;
use App\Filament\Resources\HomeConfigs\Pages\EditHomeConfig;
use App\Filament\Resources\HomeConfigs\Pages\ListHomeConfigs;
use App\Filament\Resources\HomeConfigs\Pages\ViewHomeConfig;
use App\Filament\Resources\HomeConfigs\Schemas\HomeConfigForm;
use App\Filament\Resources\HomeConfigs\Schemas\HomeConfigInfolist;
use App\Filament\Resources\HomeConfigs\Tables\HomeConfigsTable;
use App\Models\HomeConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeConfigResource extends Resource
{
    protected static ?string $model = HomeConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'yes';

    public static function form(Schema $schema): Schema
    {
        return HomeConfigForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HomeConfigInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeConfigsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeConfigs::route('/'),
            'create' => CreateHomeConfig::route('/create'),
            'view' => ViewHomeConfig::route('/{record}'),
            'edit' => EditHomeConfig::route('/{record}/edit'),
        ];
    }
}
