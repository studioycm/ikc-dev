<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Filament\Resources\PrevShowResultResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrevShowDogRelationManager extends RelationManager
{
    protected static string $relationship = 'showDogs';

    protected static ?string $recordTitleAttribute = 'SagirID';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Show Entries');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ShowID')
                    ->label(__('Show ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
                TextColumn::make('show.TitleName')
                    ->label(__('Show title'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('show.StartDate')
                    ->label(__('Start Date'))
                    ->date()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('arena.GroupName')
                    ->label(__('Arena'))
                    ->description(fn(Model $record): string => $record->ArenaID)
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('showClass.ClassName')
                    ->label(__('Class'))
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('result.DataID')
                    ->label(__('Result'))
                    ->url(function ($state) {
                        return $state ? PrevShowResultResource::getUrl('edit', ['record' => $state]) : null;
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('id')
                    ->label(__('Show Dog'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),
//                TextColumn::make('result.updated_at')
//                    ->label(__('Result'))
//                    ->since()
//                    ->dateTimeTooltip()
//                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
            ])
            ->bulkActions([
            ])
            ->defaultSort('Shows_Dogs_DB.ShowID', 'desc');
    }
}
