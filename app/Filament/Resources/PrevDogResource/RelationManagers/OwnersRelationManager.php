<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Models\PrevUser;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OwnersRelationManager extends RelationManager
{
    protected static string $relationship = 'owners';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Owner'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ownership.status')
                    ->label(__('Status'))
                    ->badge(),
                Tables\Columns\TextColumn::make('ownership.created_at')
                    ->dateTime()
                    ->label(__('Linked At')),
                Tables\Columns\TextColumn::make('ownership.updated_at')
                    ->dateTime()
                    ->label(__('Updated At')),
            ])
            ->filters([
                // Optionally filter by status
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'current' => __('Current'),
                        'historic' => __('Historic'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!filled($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->wherePivot('status', $data['value']);
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('Attach Owner'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value) => PrevUser::query()->find($value)?->name);
                    })
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'current' => __('Current'),
                                'historic' => __('Historic'),
                            ])
                            ->default('current')
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit Pivot'))
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'current' => __('Current'),
                                'historic' => __('Historic'),
                            ])
                            ->required(),
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
