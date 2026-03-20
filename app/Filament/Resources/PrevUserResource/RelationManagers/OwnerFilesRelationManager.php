<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OwnerFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'ownerFiles';

    protected static ?string $recordTitleAttribute = 'file_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Owner Files');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label(__('File Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('encrypt_key')
                    ->label(__('Encrypt Key'))
                    ->toggleable(),
                TextColumn::make('file')
                    ->label(__('File'))
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View File'))
                    ->infolist([
                        TextEntry::make('file_name')->label(__('File Name')),
                        TextEntry::make('encrypt_key')->label(__('Encrypt Key')),
                        TextEntry::make('file')->label(__('File')),
                        TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                        TextEntry::make('updated_at')->label(__('Updated At'))->dateTime(),
                    ])
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([]);
    }
}
