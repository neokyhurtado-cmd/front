<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Contenido Principal')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Textarea::make('excerpt')
                            ->label('Resumen/Extracto')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        RichEditor::make('body')
                            ->label('Contenido')
                            ->columnSpanFull(),
                    ]),

                Section::make('Configuración')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'draft' => 'Borrador',
                                'scheduled' => 'Programado',
                                'published' => 'Publicado',
                                'archived' => 'Archivado'
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'news' => 'Noticia',
                                'educational' => 'Educativo',
                                'analysis' => 'Análisis'
                            ])
                            ->default('news')
                            ->required(),
                        
                        Toggle::make('evergreen')
                            ->label('Contenido Perenne')
                            ->default(false),
                        
                        DateTimePicker::make('publish_at')
                            ->label('Programar Publicación')
                            ->seconds(false),
                        
                        DateTimePicker::make('published_at')
                            ->label('Fecha de Publicación')
                            ->seconds(false),
                    ]),

                Section::make('Fuente y Media')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('source')
                            ->label('Fuente'),
                        
                        TextInput::make('source_url')
                            ->label('URL Fuente')
                            ->url(),
                        
                        TextInput::make('image_url')
                            ->label('URL de Imagen')
                            ->url()
                            ->columnSpanFull(),
                        
                        TagsInput::make('tags')
                            ->label('Etiquetas')
                            ->columnSpanFull(),
                    ]),

                Section::make('SEO')
                    ->columns(1)
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Título')
                            ->maxLength(60),
                        
                        Textarea::make('meta_description')
                            ->label('Meta Descripción')
                            ->rows(3)
                            ->maxLength(160),
                        
                        TextInput::make('canonical_url')
                            ->label('URL Canónica')
                            ->url(),
                    ]),
            ]);
    }
}
