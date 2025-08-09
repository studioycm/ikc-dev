<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class BreedsRelationManager extends RelationManager
{
    protected static string $relationship = 'breeds';

    protected static ?string $recordTitleAttribute = 'BreedName';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
                TextColumn::make('BreedName')->label('Hebrew Name')->searchable()->sortable(),
                TextColumn::make('BreedNameEN')->label('English Name')->searchable()->sortable(),
                TextColumn::make('BreedCode')->numeric(decimalPlaces: 0, thousandsSeparator: '')->sortable(),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
