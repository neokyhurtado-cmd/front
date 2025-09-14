<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(60),
                    
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'educational' => 'info',
                        'news' => 'warning',
                        default => 'gray',
                    })
                    ->label('Tipo'),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'published' => 'success',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->label('Estado'),
                    
                TextColumn::make('publish_at')
                    ->label('Programado')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                    
                TextColumn::make('published_at')
                    ->label('Publicado')
                    ->since()
                    ->sortable(),
                    
                IconColumn::make('evergreen')
                    ->label('Evergreen')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                IconColumn::make('is_pinned')
                    ->label('Destacado')
                    ->boolean()
                    ->toggleable()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),
                    
                TextColumn::make('pin_priority')
                    ->label('Prioridad')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 8 => 'danger',
                        $state >= 5 => 'warning', 
                        $state > 0 => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "P{$state}" : '-'),
            ])
            ->defaultSort('publish_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'=>'Borrador',
                        'scheduled'=>'Programado',
                        'published'=>'Publicado',
                        'archived'=>'Archivado'
                    ]),
                SelectFilter::make('type')
                    ->options(['news'=>'Noticia','educational'=>'Educativo']),
                    
                SelectFilter::make('is_pinned')
                    ->label('Destacados')
                    ->options([
                        1 => 'Solo destacados',
                        0 => 'Solo normales'
                    ])
                    ->query(fn ($query, $data) => 
                        $data['value'] ? $query->where('is_pinned', (bool) $data['value']) : $query
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                
                Action::make('publicarAhora')
                    ->label('Publicar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Post $record) {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                            'publish_at' => $record->publish_at ?? now(),
                        ]);
                    }),
                    
                Action::make('programarSlot')
                    ->label('Programar')
                    ->color('warning')
                    ->action(function (Post $record) {
                        $slots = [8,12,16,20];
                        $today = now()->startOfDay();
                        foreach ($slots as $h) {
                            $slot = $today->copy()->setTime($h,0);
                            if (!Post::where('publish_at',$slot)->exists()) {
                                $record->update(['status'=>'scheduled','publish_at'=>$slot]);
                                break;
                            }
                        }
                    }),
                    
                Action::make('destacar')
                    ->label('Destacar')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (Post $record) {
                        $record->pin(5);
                        cache()->forget('home:v1:');
                    })
                    ->visible(fn (Post $record) => !$record->is_pinned),
                    
                Action::make('quitarDestaque')
                    ->label('Quitar destaque')
                    ->icon('heroicon-o-star')
                    ->color('gray')
                    ->action(function (Post $record) {
                        $record->unpin();
                        cache()->forget('home:v1:');
                    })
                    ->visible(fn (Post $record) => $record->is_pinned),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    
                    BulkAction::make('destacarSeleccionados')
                        ->label('Destacar seleccionados')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(fn (Post $post) => $post->pin(5));
                            cache()->forget('home:v1:');
                        }),
                        
                    BulkAction::make('quitarDestaqueSeleccionados')
                        ->label('Quitar destaque seleccionados')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->action(function ($records) {
                            $records->each(fn (Post $post) => $post->unpin());
                            cache()->forget('home:v1:');
                        }),
                ]),
            ]);
    }
}
