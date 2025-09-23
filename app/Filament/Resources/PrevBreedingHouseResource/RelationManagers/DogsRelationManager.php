<?php

namespace App\Filament\Resources\PrevBreedingHouseResource\RelationManagers;

use App\Filament\Resources\PrevDogResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DogsRelationManager extends RelationManager
{
    protected static string $relationship = 'dogs';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('dog/model/general.labels.plural');
    }

    public function table(Table $table): Table
    {
        return $table
//            ->modifyQueryUsing(fn (Builder $q) => $q->with(['breed']))
            ->columns([
                TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->toggleable(),
                TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->toggleable(),
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => PrevDogResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab()
                    ->label(__('Open Dog')),
            ])
            ->bulkActions([]);
    }
}
