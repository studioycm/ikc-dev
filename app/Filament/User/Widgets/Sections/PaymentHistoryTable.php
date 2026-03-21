<?php

namespace App\Filament\User\Widgets\Sections;

use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevPayment;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PaymentHistoryTable extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $prevUserId = $this->getCurrentPrevUserId();
        $prevUserPhone = $this->getCurrentPrevUserPhone();

        return $table
            ->query(
                PrevPayment::query()
                    ->when(
                        blank($prevUserId) && blank($prevUserPhone),
                        fn($query) => $query->whereRaw('1 = 0'),
                        fn($query) => $query->where('mobile_phone', 'like', '%' . $prevUserPhone . '%')
                            ->orWhere('created_by', $prevUserId)
                    )
                    ->with(['club:id,Name', 'breed:id,BreedName', 'dog:id,SagirID,Heb_Name,Eng_Name'])
                    ->orderByDesc('payment_date_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('approval_number')
                    ->label(__('Approval Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('desc')
                    ->label(__('Description'))
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money(currency: 'ILS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('dog.SagirID')
                    ->label(__('Dog'))
                    ->description(fn(PrevPayment $record): ?string => $record->dog?->full_name)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_date_time')
                    ->label(__('Paid at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn(PrevPayment $record): string => __('Payment #:id', ['id' => $record->id]))
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist->schema([
                        Section::make(__('Payment Details'))
                            ->schema([
                                TextEntry::make('approval_number')->label(__('Approval Number')),
                                TextEntry::make('desc')->label(__('Description')),
                                TextEntry::make('amount')->label(__('Amount'))->money(currency: 'ILS'),
                                TextEntry::make('payment_date_time')->label(__('Paid At'))->dateTime(),
                                TextEntry::make('club.Name')->label(__('Club')),
                                TextEntry::make('breed.BreedName')->label(__('Breed')),
                                TextEntry::make('dog.SagirID')->label(__('Dog')),
                                TextEntry::make('dog.full_name')->label(__('Dog Name')),
                            ])
                            ->columns(2),
                    ])),
            ])
            ->heading(__('Payment History'))
            ->description(__('Review your latest payments, approvals, and linked dogs.'))
            ->defaultSort('payment_date_time', 'desc')
            ->paginated([5, 10, 15, 'all'])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No Payments Found'))
            ->emptyStateDescription(__('Payments linked to your account will appear here.'))
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
