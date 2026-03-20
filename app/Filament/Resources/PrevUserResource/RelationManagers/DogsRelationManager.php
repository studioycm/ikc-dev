<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DogsRelationManager extends RelationManager
{
    protected static string $relationship = 'dogs';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Dogs');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('ownership.created_at', 'desc')
            ->columns([
                TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('Dog'))
                    ->searchable(['Heb_Name', 'Eng_Name'])
                    ->wrap(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->toggleable(),
                TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('ownership.status')
                    ->label(__('Ownership Status'))
                    ->badge(),
                TextColumn::make('ownership.created_at')
                    ->label(__('Linked At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Dog'))
                    ->url(fn(PrevDog $record): string => PrevDogResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
