<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Group::make()->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $set('meta_title', Str::limit($state.' | Panorama Ingeniería IA', 60, ''))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Se genera automáticamente al guardar.'),

                    TextInput::make('image_url')
                        ->label('URL de imagen')
                        ->url()
                        ->nullable(),

                    Textarea::make('excerpt')
                        ->label('Extracto (SEO)')
                        ->rows(3)
                        ->helperText('Resumen corto para listados y meta descripción.'),

                    RichEditor::make('body')
                        ->label('Contenido')
                        ->columnSpanFull()
                        ->required(),
                ])->columnSpan(8),

                Group::make()->schema([
                    Select::make('type')
                        ->label('Tipo')
                        ->options(['news'=>'Noticia','educational'=>'Educativo'])
                        ->default('news'),

                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'draft'=>'Borrador',
                            'scheduled'=>'Programado',
                            'published'=>'Publicado',
                            'archived'=>'Archivado',
                        ])->default('draft'),

                    Toggle::make('evergreen')
                        ->label('Contenido evergreen')
                        ->default(false),

                    Toggle::make('pinned')
                        ->label('Fijar en Home (legacy)')
                        ->default(false)
                        ->live()
                        ->helperText('Sistema anterior. Usar "Destacado" mejor.'),

                    Toggle::make('is_pinned')
                        ->label('⭐ Destacado')
                        ->default(false)
                        ->live()
                        ->helperText('Aparece en fila horizontal "Destacadas". IA automática.'),

                    DateTimePicker::make('pinned_until')
                        ->label('Destacado hasta')
                        ->seconds(false)
                        ->native(false)
                        ->visible(fn ($get) => $get('pinned') || $get('is_pinned'))
                        ->helperText('Si se deja vacío, se destaca por 30 días'),

                    DateTimePicker::make('publish_at')
                        ->label('Publicar el')
                        ->seconds(false),

                    TextInput::make('seo_keywords')
                        ->label('Keywords SEO')
                        ->helperText('Separadas por coma. Se generan con IA.'),

                    TextInput::make('meta_title')
                        ->label('Meta title')
                        ->maxLength(60),

                    Textarea::make('meta_description')
                        ->label('Meta description')
                        ->rows(2)
                        ->helperText('Máx. 160 caracteres.'),

                    TextInput::make('image_source_label')
                        ->label('Fuente de imagen')
                        ->placeholder('Ej: Reuters, AP'),

                    TextInput::make('image_source_url')
                        ->label('URL fuente imagen')
                        ->url()
                        ->nullable(),

                    TagsInput::make('tags')
                        ->label('Tags')
                        ->separator(',')
                        ->suggestions(['movilidad', 'transporte', 'bogota', 'señalización', 'vial', 'urbano', 'TransMilenio', 'cierres', 'seguridad vial']),

                    FileUpload::make('featured_image')
                        ->label('Imagen destacada')
                        ->directory('posts')
                        ->image()
                        ->imageEditor()
                        ->visibility('public')
                        ->downloadable()
                        ->openable(),
                ])->columnSpan(4),
            ]);
    }
}
