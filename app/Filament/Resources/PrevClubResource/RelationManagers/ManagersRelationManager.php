<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

use App\Filament\Resources\PrevUserResource;
use App\Models\PrevClub;
use App\Models\PrevSkill;
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

class ManagersRelationManager extends RelationManager
{
    protected static string $relationship = 'managers';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Staff');
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
                    ->label(__('Manager'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false),
                TextColumn::make('club_titles_text')
                    ->label(__('Title'))
                    ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->managerTitleLabelsForUser($record)->join(', ') ?: '-'),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->toggleable(),
                TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
                    ->toggleable(),
                TextColumn::make('pivot.created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('pivot.updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('created_between')
                    ->form([
                        DatePicker::make('created_from')->label(__('Created From')),
                        DatePicker::make('created_until')->label(__('Created Until')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['created_from'] ?? null, fn(Builder $query, $date): Builder => $query->wherePivot('created_at', '>=', $date))
                        ->when($data['created_until'] ?? null, fn(Builder $query, $date): Builder => $query->wherePivot('created_at', '<=', $date . ' 23:59:59'))),
                Filter::make('updated_between')
                    ->form([
                        DatePicker::make('updated_from')->label(__('Updated From')),
                        DatePicker::make('updated_until')->label(__('Updated Until')),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query
                        ->when($data['updated_from'] ?? null, fn(Builder $query, $date): Builder => $query->wherePivot('updated_at', '>=', $date))
                        ->when($data['updated_until'] ?? null, fn(Builder $query, $date): Builder => $query->wherePivot('updated_at', '<=', $date . ' 23:59:59'))),
                Filter::make('has_titles')
                    ->label(__('Has titles'))
                    ->query(fn(Builder $query): Builder => $query->whereHas('skills', fn(Builder $skillsQuery): Builder => $skillsQuery->whereIn('skills.id', PrevClub::CLUB_STAFF_SKILL_IDS))),
                SelectFilter::make('skill_id')
                    ->label(__('Title'))
                    ->options(
                        PrevSkill::query()
                            ->whereIn('id', PrevClub::CLUB_STAFF_SKILL_IDS)
                            ->orderBy('skill_name')
                            ->pluck('skill_name', 'id')
                            ->all(),
                    )
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn(Builder $query, $skillId): Builder => $query->whereHas('skills', fn(Builder $skillsQuery): Builder => $skillsQuery->whereKey($skillId)),
                    )),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('Attach Manager'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Manager'))
                    ->infolist([
                        TextEntry::make('name')->label(__('Name')),
                        TextEntry::make('club_titles_text')
                            ->label(__('Title'))
                            ->state(fn(PrevUser $record): string => $this->getOwnerRecord()->managerTitleLabelsForUser($record)->join(', ') ?: '-'),
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
                        TextEntry::make('pivot.created_at')->label(__('Created At'))->dateTime(),
                        TextEntry::make('pivot.updated_at')->label(__('Updated At'))->dateTime(),
                    ])
                    ->modalHeading(fn(PrevUser $record): string => $record->name)
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('editManager')
                            ->label(__('Edit Manager'))
                            ->icon('heroicon-o-pencil-square')
                            ->url(fn(PrevUser $record): string => PrevUserResource::getUrl('edit', ['record' => $record]))
                            ->openUrlInNewTab(),
                    ]),
                Tables\Actions\DetachAction::make()
                    ->label(__('Detach')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
