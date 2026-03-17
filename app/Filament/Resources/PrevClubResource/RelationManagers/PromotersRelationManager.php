<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

use App\Filament\Resources\PrevUserResource;
use App\Models\PrevBreedUser;
use App\Models\PrevClub;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromotersRelationManager extends RelationManager
{
    protected static string $relationship = 'managers';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Promoters');
    }

    protected function getTableQuery(): Builder
    {
        /** @var PrevClub $club */
        $club = $this->getOwnerRecord();

        return $club->promotersQuery()->with([
            'promotedBreeds' => fn($query) => $query->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $club->getKey())),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Promoter'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false),
                TextColumn::make('club_titles_text')
                    ->label(__('Title'))
                    ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->decoratePromoterUser($record)->getAttribute('club_titles_text') ?: '-'),
                TextColumn::make('club_breeds_text')
                    ->label(__('Breeds'))
                    ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->decoratePromoterUser($record)->getAttribute('club_breeds_text') ?: '-'),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->toggleable(),
                TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
                    ->state(fn(PrevUser $record): ?string => $record->normalised_phone ?? $record->mobile_phone)
                    ->toggleable(),
                TextColumn::make('promoter_created_at')
                    ->label(__('Created At'))
                    ->state(fn(PrevUser $record): ?string => $record->promotedBreeds
                        ->whereIn('id', $this->getOwnerRecord()->breeds->pluck('id'))
                        ->pluck('pivot.created_at')
                        ->filter()
                        ->sort()
                        ->first()?->toDateTimeString())
                    ->dateTime()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('users.id', $direction);
                    }),
                TextColumn::make('promoter_updated_at')
                    ->label(__('Updated At'))
                    ->state(fn(PrevUser $record): ?string => $record->promotedBreeds
                        ->whereIn('id', $this->getOwnerRecord()->breeds->pluck('id'))
                        ->pluck('pivot.updated_at')
                        ->filter()
                        ->sortDesc()
                        ->first()?->toDateTimeString())
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('created_between')
                    ->form([
                        DatePicker::make('created_from')->label(__('Created From')),
                        DatePicker::make('created_until')->label(__('Created Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn(Builder $query, $date): Builder => $query->whereHas('promotedBreeds', fn(Builder $breedQuery): Builder => $breedQuery
                                ->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $this->getOwnerRecord()->getKey()))
                                ->wherePivot('created_at', '>=', $date)))
                            ->when($data['created_until'] ?? null, fn(Builder $query, $date): Builder => $query->whereHas('promotedBreeds', fn(Builder $breedQuery): Builder => $breedQuery
                                ->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $this->getOwnerRecord()->getKey()))
                                ->wherePivot('created_at', '<=', $date . ' 23:59:59')));
                    }),
                Filter::make('updated_between')
                    ->form([
                        DatePicker::make('updated_from')->label(__('Updated From')),
                        DatePicker::make('updated_until')->label(__('Updated Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['updated_from'] ?? null, fn(Builder $query, $date): Builder => $query->whereHas('promotedBreeds', fn(Builder $breedQuery): Builder => $breedQuery
                                ->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $this->getOwnerRecord()->getKey()))
                                ->wherePivot('updated_at', '>=', $date)))
                            ->when($data['updated_until'] ?? null, fn(Builder $query, $date): Builder => $query->whereHas('promotedBreeds', fn(Builder $breedQuery): Builder => $breedQuery
                                ->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $this->getOwnerRecord()->getKey()))
                                ->wherePivot('updated_at', '<=', $date . ' 23:59:59')));
                    }),
                SelectFilter::make('breed_id')
                    ->label(__('Breed'))
                    ->options($this->getOwnerRecord()->breeds()->orderBy('BreedName')->pluck('BreedName', 'BreedsDB.id')->all())
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn(Builder $query, $breedId): Builder => $query->whereHas('promotedBreeds', fn(Builder $breedQuery): Builder => $breedQuery->where('BreedsDB.id', $breedId)),
                    )),
            ])
            ->headerActions([
                Tables\Actions\Action::make('attachPromoter')
                    ->label(__('Attach Promoter'))
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label(__('User'))
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name)
                            ->required(),
                        Forms\Components\Select::make('breed_id')
                            ->label(__('Breed'))
                            ->options($this->getOwnerRecord()->breeds()->orderBy('BreedName')->pluck('BreedName', 'BreedsDB.id')->all())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $assignment = PrevBreedUser::withTrashed()
                            ->firstOrNew([
                                'user_id' => $data['user_id'],
                                'breed_id' => $data['breed_id'],
                            ]);

                        $assignment->deleted_at = null;
                        $assignment->save();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Promoter'))
                    ->infolist([
                        TextEntry::make('name')->label(__('Name')),
                        TextEntry::make('club_titles_text')
                            ->label(__('Title'))
                            ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->decoratePromoterUser($record)->getAttribute('club_titles_text') ?: '-'),
                        TextEntry::make('club_breeds_text')
                            ->label(__('Breeds'))
                            ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->decoratePromoterUser($record)->getAttribute('club_breeds_text') ?: '-'),
                        TextEntry::make('email')
                            ->label(__('Email'))
                            ->copyable()
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500),
                        TextEntry::make('mobile_phone')
                            ->label(__('Mobile Phone'))
                            ->state(fn(PrevUser $record): ?string => $record->normalised_phone ?? $record->mobile_phone)
                            ->copyable()
                            ->copyMessage(__('filament::components/copyable.messages.copied'))
                            ->copyMessageDuration(1500),
                        TextEntry::make('promoter_created_at')
                            ->label(__('Created At'))
                            ->state(fn(PrevUser $record): ?string => $this->resolvePromoterCreatedAt($record))
                            ->dateTime(),
                        TextEntry::make('promoter_updated_at')
                            ->label(__('Updated At'))
                            ->state(fn(PrevUser $record): ?string => $this->resolvePromoterUpdatedAt($record))
                            ->dateTime(),
                    ])
                    ->modalHeading(fn(PrevUser $record): string => $record->name)
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('editPromoter')
                            ->label(__('Edit Promoter'))
                            ->icon('heroicon-o-pencil-square')
                            ->url(fn(PrevUser $record): string => PrevUserResource::getUrl('edit', ['record' => $record]))
                            ->openUrlInNewTab(),
                    ]),
                Tables\Actions\Action::make('detachPromoter')
                    ->label(__('Detach'))
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('breed_ids')
                            ->label(__('Breeds'))
                            ->multiple()
                            ->options(fn(PrevUser $record): array => $record->promotedBreeds
                                ->whereIn('id', $this->getOwnerRecord()->breeds->pluck('id'))
                                ->pluck('BreedName', 'id')
                                ->all())
                            ->required(),
                    ])
                    ->action(function (PrevUser $record, array $data): void {
                        PrevBreedUser::query()
                            ->where('user_id', $record->getKey())
                            ->whereIn('breed_id', $data['breed_ids'])
                            ->delete();
                    }),
            ])
            ->bulkActions([]);
    }

    protected function resolvePromoterCreatedAt(PrevUser $record): ?string
    {
        return $record->promotedBreeds
            ->whereIn('id', $this->getOwnerRecord()->breeds->pluck('id'))
            ->pluck('pivot.created_at')
            ->filter()
            ->sort()
            ->first()?->toDateTimeString();
    }

    protected function resolvePromoterUpdatedAt(PrevUser $record): ?string
    {
        return $record->promotedBreeds
            ->whereIn('id', $this->getOwnerRecord()->breeds->pluck('id'))
            ->pluck('pivot.updated_at')
            ->filter()
            ->sortDesc()
            ->first()?->toDateTimeString();
    }
}
