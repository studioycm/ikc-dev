<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class BreedsRelationManager extends RelationManager
{
    protected static string $relationship = 'breeds';

    protected static ?string $recordTitleAttribute = 'BreedName';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Breeds');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('ID'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('BreedName')->label(__('Hebrew Name'))->searchable()->sortable(),
                TextColumn::make('BreedNameEN')->label(__('English Name'))->searchable()->sortable(),
                TextColumn::make('BreedCode')->label(__('Breed Code'))->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
