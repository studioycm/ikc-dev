<?php

namespace App\Filament\Resources\PrevUserResource\RelationManagers;

use App\Filament\Resources\PrevClubResource;
use App\Models\PrevClub;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ClubsRelationManager extends RelationManager
{
    protected static string $relationship = 'clubs';

    protected static ?string $recordTitleAttribute = 'Name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Clubs');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query): Builder => $query->reorder('club2user.expire_date', 'asc'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('Name')
                    ->label(__('Club'))
                    ->searchable(['Name', 'EngName'])
                    ->sortable(),
                TextColumn::make('membership.type')
                    ->label(__('Type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('membership.payment_status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->toggleable(),
                IconColumn::make('membership.forbidden')
                    ->label(__('Forbidden'))
                    ->boolean(),
                TextColumn::make('membership.expire_date')
                    ->label(__('Expires At'))
                    ->dateTime()
                    ->sortable(query: fn(Builder $query, string $direction): Builder => $query->orderBy('club2user.expire_date', $direction)),
                TextColumn::make('membership.created_at')
                    ->label(__('Linked At'))
                    ->dateTime()
                    ->sortable(query: fn(Builder $query, string $direction): Builder => $query->orderBy('club2user.created_at', $direction))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('membership_type')
                    ->label(__('Type'))
                    ->options(fn(): array => $this->getOwnerRecord()->clubs()
                        ->select('club2user.type')
                        ->distinct()
                        ->whereNotNull('club2user.type')
                        ->orderBy('club2user.type')
                        ->pluck('club2user.type', 'club2user.type')
                        ->all())
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn(Builder $query, string $value): Builder => $query->wherePivot('type', $value),
                    )),
                SelectFilter::make('membership_payment_status')
                    ->label(__('Payment Status'))
                    ->options(fn(): array => $this->getOwnerRecord()->clubs()
                        ->select('club2user.payment_status')
                        ->distinct()
                        ->whereNotNull('club2user.payment_status')
                        ->orderBy('club2user.payment_status')
                        ->pluck('club2user.payment_status', 'club2user.payment_status')
                        ->mapWithKeys(fn($label, $value): array => [(string)$value => (string)$label])
                        ->all())
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        ($data['value'] ?? null) !== null && $data['value'] !== '',
                        fn(Builder $query): Builder => $query->wherePivot('payment_status', $data['value']),
                    )),
                TernaryFilter::make('membership_forbidden')
                    ->label(__('Forbidden'))
                    ->queries(
                        true: fn(Builder $query): Builder => $query->wherePivot('forbidden', true),
                        false: fn(Builder $query): Builder => $query->wherePivot('forbidden', false),
                        blank: fn(Builder $query): Builder => $query,
                    ),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Club'))
                    ->url(fn(PrevClub $record): string => PrevClubResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
