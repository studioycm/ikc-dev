<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use App\Filament\Resources\PrevUserRequestResource;
use App\Models\PrevUserRequest;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'ownedRequests';

    protected static ?string $recordTitleAttribute = 'topic';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('User Requests');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('record_date_time', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                IconColumn::make('IsDone')
                    ->label(__('Done'))
                    ->boolean(),
                TextColumn::make('doneBy.name')
                    ->label(__('Done By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Request'))
                    ->url(fn(PrevUserRequest $record): string => PrevUserRequestResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
