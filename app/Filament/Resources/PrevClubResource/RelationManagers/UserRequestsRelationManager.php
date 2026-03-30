<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

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
    protected static string $relationship = 'userRequests';

    protected static ?string $recordTitleAttribute = 'topic';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('User Requests');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(
                ['owner', 'dog', 'doneBy']
            ))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('owner.name')
                    ->label(__('Owner'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('owner_name')
                    ->label(__('Owner Name'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('Cost'))
                    ->money('NIS', 0, 'he')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Payment Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name)
                    ->sortable()
                    ->searchable(['sagirID', 'eng_name', 'heb_name'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('doneBy.name')
                    ->label(__('Done By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'email'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                IconColumn::make('IsDone')
                    ->label(__('Done'))
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('DoneDate')
                    ->label(__('Done Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('record_date_time')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
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
