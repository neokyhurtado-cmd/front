<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Imagen')
                    ->width(60)
                    ->height(60)
                    ->circular(),
                
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'scheduled',
                        'success' => 'published',
                        'danger' => 'archived',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-clock' => 'scheduled',
                        'heroicon-o-check-circle' => 'published',
                        'heroicon-o-archive-box' => 'archived',
                    ]),

                BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'news',
                        'success' => 'educational',
                        'warning' => 'analysis',
                    ]),

                TextColumn::make('source')
                    ->label('Fuente')
                    ->searchable()
                    ->limit(20)
                    ->toggleable(),

                IconColumn::make('evergreen')
                    ->label('Perenne')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->since()
                    ->placeholder('No publicado'),

                TextColumn::make('publish_at')
                    ->label('Programado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'scheduled' => 'Programado',
                        'published' => 'Publicado',
                        'archived' => 'Archivado',
                    ]),
                
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'news' => 'Noticia',
                        'educational' => 'Educativo',
                        'analysis' => 'Análisis',
                    ]),

                SelectFilter::make('evergreen')
                    ->label('¿Perenne?')
                    ->options([
                        1 => 'Sí',
                        0 => 'No',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('view_blog')
                    ->label('Ver en Blog')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record): string => route('post.show', $record->slug))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay posts')
            ->emptyStateDescription('Aún no se han creado posts. Los posts se generan automáticamente desde RSS.')
            ->striped();
    }
}
