<?php

namespace App\Filament\Resources\PrevBreedingHouseResource\RelationManagers;

use App\Filament\Resources\PrevUserResource;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Users');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label(__('Name'))->searchable(),
                TextColumn::make('email')->label(__('Email'))->toggleable(),
                TextColumn::make('mobile_phone')->label(__('Phone'))->toggleable(),
                TextColumn::make('pivot.created_at')->dateTime()->label(__('Linked At')),
                TextColumn::make('pivot.updated_at')->dateTime()->label(__('Updated At')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('Attach User'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => PrevUser::selectOptions($search))
                            ->getOptionLabelUsing(fn($value) => PrevUser::query()->find($value)?->name);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View User'))
                    ->url(fn(PrevUser $record) => PrevUserResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\DetachAction::make()->label(__('Detach')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
