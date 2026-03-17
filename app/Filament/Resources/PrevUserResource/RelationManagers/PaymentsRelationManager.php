<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use App\Filament\Resources\PrevPaymentResource;
use App\Models\PrevPayment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'createdPayments';

    protected static ?string $recordTitleAttribute = 'approval_number';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Payments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date_time', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('approval_number')
                    ->label(__('Approval Number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('desc')
                    ->label(__('Description'))
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->toggleable(),
                TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevPayment $record): ?string => $record->dog?->full_name)
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('updatedBy.name')
                    ->label(__('Updated By'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Payment'))
                    ->url(fn(PrevPayment $record): string => PrevPaymentResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
