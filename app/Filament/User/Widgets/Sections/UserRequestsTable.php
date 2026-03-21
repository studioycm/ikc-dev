<?php

namespace App\Filament\User\Widgets\Sections;

use App\Enums\Legacy\LegacyUserRequestTopic;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevUserRequest;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class UserRequestsTable extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $prevUserId = $this->getCurrentPrevUserId();
        $prevUserMobilePhone = $this->getPrevUserMobilePhone();

        return $table
            ->query(
                PrevUserRequest::query()
                    ->when(
                        blank($prevUserId) && blank($prevUserMobilePhone),
                        fn($query) => $query->whereRaw('1 = 0'),
                        fn($query) => $query->where('mobile_phone', 'like', '%' . $prevUserMobilePhone . '%')
                            ->orWhere('owner_id', $prevUserId)
                    )
                    ->with(['club:id,Name', 'dog:id,SagirID,Heb_Name,Eng_Name', 'vetAuth:id,name,vet_email'])
                    ->orderByDesc('updated_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->badge()
                    ->description(function ($state, PrevUserRequest $record): ?string {
                        if ($state === null) {
                            return __('Topic') . " " . __('Missing');
                        }

                        return match ($state->value) {
                            'pedigree_paper_request' => $record->paper_request_type
                                ? $record->paper_request_type->getLabel()
                                : __('Pedigree Type') . " " . __('Missing'),

                            'champion_diploma_request' => $record->champion_certificate_type
                                ? $record->champion_certificate_type->getLabel()
                                : __('Champion Certificate') . " " . __('Missing'),

                            'Payment of pelvic / elbow photo decoding' => $record->total_amount
                                ? Number::currency($record->total_amount, in: 'ILS', locale: 'he_IL', precision: 0)
                                : __('Price') . " " . __('Missing'),
                            'agra_form' => $record->vetAuth
                                ? $record->vetAuth->name . " (" . $record->vetAuth->vet_email . ")"
                                : __('Veterinarian Authority') . " " . __('Missing'),
                            default => '',
                        };
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('club.Name')
                    ->label(__('Club'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('dog.SagirID')
                    ->label(__('dog/model/general.labels.singular'))
                    ->description(fn(PrevUserRequest $record): ?string => $record->dog?->full_name)
                    ->sortable()
                    ->searchable(['DogsDB.SagirID', 'DogsDB.eng_name', 'DogsDB.heb_name'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Amount'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: ',')
                    ->money(currency: 'ILS')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_date_time')
                    ->label(__('Payment Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending payment' => 'warning',
                        'payment done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('record_date_time')
                    ->label(__('Recorded at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('IsDone')
                    ->label(__('Done'))
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('DoneDate')
                    ->label(__('Done Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Requested'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending payment' => __('Pending Payment'),
                        'payment done' => __('Payment Done'),
                    ]),
                Tables\Filters\SelectFilter::make('club')
                    ->label(__('Club'))
                    ->relationship('club', 'Name')
                    ->searchable(['Name', 'EngName'])
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('topic')
                    ->label(__('Topic'))
                    ->options(LegacyUserRequestTopic::class)
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn(PrevUserRequest $record): string => __('Request #:id', ['id' => $record->id]))
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist->schema([
                        Section::make(__('Request Details'))
                            ->schema([
                                TextEntry::make('topic')->label(__('Topic')),
                                TextEntry::make('status')->label(__('Status')),
                                TextEntry::make('club.Name')->label(__('Club')),
                                TextEntry::make('dog.SagirID')->label(__('dog/model/general.labels.singular')),
                                TextEntry::make('dog.full_name')->label(__('Dog name')),
                                TextEntry::make('total_amount')->label(__('Amount'))->money(currency: 'ILS'),
                                TextEntry::make('payment_date_time')->label(__('Payment Date'))->dateTime(),
                                TextEntry::make('vetAuth.name')->label(__('Veterinarian Authority')),
                                TextEntry::make('vetAuth.vet_email')->label(__('Authority Email')),
                            ])
                            ->columns(2),
                    ])),
            ])
            ->heading(__('Requests'))
            ->description(__('Track your submitted requests and their payment status.'))
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 15, 'all'])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No Requests Found'))
            ->emptyStateDescription(__('Your submitted registration and paperwork requests will appear here.'))
            ->emptyStateIcon('heroicon-o-document-text');
    }

    private function getPrevUserMobilePhone()
    {
        return auth()->user()?->prevUser?->mobile_phone;
    }
}
