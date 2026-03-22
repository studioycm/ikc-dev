<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Filament\Resources\PrevPaymentResource;
use App\Models\PrevPayment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

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
                TextColumn::make('amount')
                    ->label(__('Cost'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('payment_date_time')
                    ->label(__('Payment Date Time'))
                    ->dateTime()
                    ->sortable(),
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
