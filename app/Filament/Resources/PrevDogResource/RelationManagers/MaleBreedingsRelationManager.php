<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\Resources\PrevBreedingResource;
use App\Models\PrevBreeding;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MaleBreedingsRelationManager extends RelationManager
{
    protected static string $relationship = 'maleBreedings';

    protected static ?string $recordTitleAttribute = 'id';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Breedings');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->GenderID === LegacyDogGender::Male;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('birthing_date', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('Breeding'))
                    ->formatStateUsing(fn($state): string => '#' . $state)
                    ->sortable(),
                TextColumn::make('female.SagirID')
                    ->label(__('Female'))
                    ->description(fn(PrevBreeding $record): ?string => $record->female?->full_name)
                    ->toggleable(),
                TextColumn::make('BreddingDate')
                    ->label(__('Breeding Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('birthing_date')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('live_male_puppie')
                    ->label(__('Live Male Puppies'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(),
                TextColumn::make('live_female_puppie')
                    ->label(__('Live Female Puppies'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Breeding'))
                    ->url(fn(PrevBreeding $record): string => PrevBreedingResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
