<?php

namespace App\Filament\Resources\PrevClubResource\RelationManagers;

use App\Filament\Exports\PrevClubMemberExporter;
use App\Filament\Resources\PrevUserResource;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Members');
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
                TextColumn::make('name')
                    ->label(__('Member'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false),
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
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(query: fn(Builder $query, string $direction): Builder => $query->orderBy('club2user.created_at', $direction))
                    ->toggleable(),
                TextColumn::make('membership.updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable(query: fn(Builder $query, string $direction): Builder => $query->orderBy('club2user.updated_at', $direction))
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->toggleable(),
                TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
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
                SelectFilter::make('membership_type')
                    ->label(__('Type'))
                    ->options(fn(): array => $this->getOwnerRecord()->members()
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
                    ->options(fn(): array => $this->getOwnerRecord()->members()
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
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export Members'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevClubMemberExporter::class),
                Tables\Actions\AttachAction::make()
                    ->label(__('Attach Member'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search): array => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->find($value)?->name);
                    })
                    ->form([
                        Forms\Components\TextInput::make('type')->label(__('Type'))->maxLength(255),
                        Forms\Components\TextInput::make('status')->label(__('Status'))->default('active')->required(),
                        Forms\Components\TextInput::make('payment_status')->label(__('Payment Status'))->numeric()->default(1),
                        Forms\Components\Toggle::make('forbidden')->label(__('Forbidden'))->default(false),
                        DatePicker::make('expire_date')->label(__('Expires At')),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Member'))
                    ->infolist([
                        TextEntry::make('name')->label(__('Name')),
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
                        TextEntry::make('membership.type')->label(__('Type')),
                        TextEntry::make('membership.status')->label(__('Status')),
                        TextEntry::make('membership.created_at')->label(__('Created At'))->dateTime(),
                        TextEntry::make('membership.updated_at')->label(__('Updated At'))->dateTime(),
                    ])
                    ->modalHeading(fn(PrevUser $record): string => $record->name)
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('editMember')
                            ->label(__('Edit Member'))
                            ->icon('heroicon-o-pencil-square')
                            ->url(fn(PrevUser $record): string => PrevUserResource::getUrl('edit', ['record' => $record]))
                            ->openUrlInNewTab(),
                    ]),
                Tables\Actions\EditAction::make('edit-membership')
                    ->label(__('Edit Membership'))
                    ->form([
                        Forms\Components\TextInput::make('type')->label(__('Type'))->maxLength(255),
                        Forms\Components\TextInput::make('status')->label(__('Status'))->required(),
                        Forms\Components\TextInput::make('payment_status')->label(__('Payment Status'))->numeric(),
                        Forms\Components\Toggle::make('forbidden')->label(__('Forbidden')),
                        DatePicker::make('expire_date')->label(__('Expires At')),
                    ]),
                Tables\Actions\DetachAction::make()
                    ->label(__('Detach')),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevClubMemberExporter::class),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
