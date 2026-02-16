<?php

namespace App\Filament\User\Widgets;

use App\Models\PrevClubUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ClubMembershipsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $prevUserId = auth()->user()?->prevUser?->id;

        if (!$prevUserId) {
            return $table->query(PrevClubUser::query()->whereRaw('1 = 0'));
        }

        // Get user's dogs breeds to find related clubs
        $userBreedIds = auth()->user()?->prevUser?->dogs()
            ->with('breed:id,BreedCode')
            ->get()
            ->pluck('breed.id')
            ->unique()
            ->filter()
            ->toArray() ?? [];

        return $table
            ->query(
                PrevClubUser::query()
                    ->where('user_id', $prevUserId)
                    ->whereHas('club', function (Builder $query) use ($userBreedIds) {
                        $query->whereHas('breeds', function (Builder $q) use ($userBreedIds) {
                            if (!empty($userBreedIds)) {
                                $q->whereIn('BreedsDB.id', $userBreedIds);
                            }
                        });
                    })
                    ->with([
                        'club:id,Name,Logo,ClubCode',
                        'club.breeds:BreedsDB.id,BreedsDB.BreedName,BreedsDB.BreedNameEN',
                    ])
                    ->orderBy('club_id')
                    ->orderBy('expire_date', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('club.Name')
                    ->label('Club')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn($state): string => match ($state) {
                        'Main' => __('Main'),
                        'Sub' => __('Sub'),
                        default => __('Main'),
                    })
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'Main' => 'info',
                        'Sub' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('computed_status')
                    ->label('Status')
                    ->formatStateUsing(fn($state): string => match ($state) {
                        1 => 'Active',
                        0 => 'Inactive',
                        2 => 'Pending Payment',
                        3 => 'Expired',
                        default => 'Unknown',
                    })
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                        2 => 'warning',
                        3 => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Valid From')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Valid Until')
                    ->date('Y-m-d')
                    ->description(fn(PrevClubUser $record): string => $record->expiration_human)
                    ->color(fn(PrevClubUser $record): string => $record->getExpirationColor())
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status_code')
                    ->label('Payment')
                    ->formatStateUsing(fn(?int $state): string => match ($state) {
                        1 => 'Paid',
                        0 => 'Pending',
                        null => 'N/A',
                        default => 'Unknown',
                    })
                    ->badge()
                    ->color(fn(?int $state): string => match ($state) {
                        1 => 'success',
                        0 => 'warning',
                        null => 'gray',
                        default => 'gray',
                    }),
            ])
            ->groups([
                Group::make('club.Name')
                    ->label(__('Club'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('club.Name')
            ->groupingSettingsHidden()
            ->actions([
                Tables\Actions\Action::make('renew')
                    ->hiddenLabel()
                    ->tooltip(__('Renew'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->form([
                        Select::make('membership_type')
                            ->label('Membership Type')
                            ->options([
                                'Main' => __('Main'),
                                'Sub' => __('Sub'),
                            ])
                            ->default(fn(PrevClubUser $record) => $record->type)
                            ->required(),
                        Select::make('duration')
                            ->label('Duration')
                            ->options([
                                '1_year' => '1 Year',
                                '2_years' => '2 Years',
                                '3_years' => '3 Years',
                            ])
                            ->default('1_year')
                            ->required(),
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'credit_card' => 'Credit Card',
                                'bank_transfer' => 'Bank Transfer',
                                'paypal' => 'PayPal',
                                'cash' => 'Cash',
                            ])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Total Amount')
                            ->prefix('₪')
                            ->numeric()
                            ->default(500)
                            ->disabled()
                            ->dehydrated(false),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Any additional information...'),
                    ])
                    ->modalHeading(fn(PrevClubUser $record): string => "Renew Membership - {$record->club->Name}")
                    ->modalDescription('Complete the form to renew your club membership')
                    ->modalSubmitActionLabel('Submit Renewal Request')
                    ->action(function (array $data, PrevClubUser $record) {
                        // Placeholder action - will be implemented later
                        \Filament\Notifications\Notification::make()
                            ->title('Renewal Request Submitted')
                            ->body('Your membership renewal request has been submitted successfully. We will contact you soon.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('view_details')
                    ->hiddenLabel()
                    ->tooltip(__('Details'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn(PrevClubUser $record): string => "Membership Details - {$record->club->Name}")
                    ->modalContent(fn(PrevClubUser $record) => view('filament.user.modals.membership-details', [
                        'membership' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->heading('Club Memberships')
            ->description('Your active club memberships by breed associations')
            ->emptyStateHeading('No Memberships Found')
            ->emptyStateDescription('You don\'t have any active club memberships yet.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
