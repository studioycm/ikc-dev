<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use App\Filament\Resources\PrevUserActivityResource;
use App\Models\PrevUserActivity;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = 'Activity_Type';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('User Activities');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('CreationDateTime', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('Activity_Type')
                    ->label(__('Activity Type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('Activity_Desc')
                    ->label(__('Description'))
                    ->wrap()
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('createdBy.name')
                    ->label(__('Created By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                IconColumn::make('Is_Payment')
                    ->label(__('Payment'))
                    ->boolean(),
                IconColumn::make('Is_Show')
                    ->label(__('Show'))
                    ->boolean(),
                IconColumn::make('Is_Study')
                    ->label(__('Study'))
                    ->boolean(),
                TextColumn::make('CreationDateTime')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Activity'))
                    ->url(fn(PrevUserActivity $record): string => PrevUserActivityResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
