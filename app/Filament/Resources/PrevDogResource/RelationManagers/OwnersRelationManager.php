<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Filament\Resources\PrevUserResource;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OwnersRelationManager extends RelationManager
{
    protected static string $relationship = 'owners';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Owners');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('dogs2users.created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Owner'))
                    ->description(fn($record) => $record->id)
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_phone')
                    ->label(__('Phone'))
                    ->copyable()
                    ->copyMessage(fn($state) => __('Phone number') . " $state " . __('copied to clipboard'))
                    ->copyMessageDuration(1000)
                    ->icon('heroicon-o-phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->copyable()
                    ->copyMessage(fn($state) => __('Email') . " $state " . __('copied to clipboard'))
                    ->copyMessageDuration(1000)
                    ->icon('heroicon-o-envelope')
                    ->searchable()
                    ->toggleable(),
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
            ->filters([])
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
                                'current' => 'Current',
                                'old' => 'Old',
                                null => 'Unknown',
                            ])
                            ->default('current')
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Owner'))
                    // Show a simple modal instead of navigating to a non-existent View page
                    ->infolist([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('id')->label('ID'),
                            TextEntry::make('name')->label(__('Name')),
                            TextEntry::make('mobile_phone')->label(__('Phone')),
                            TextEntry::make('email')->label(__('Email')),
                            TextEntry::make('address_city')->label(__('City')),
                            TextEntry::make('address_street')->label(__('Street')),
                        ]),
                    ])
                    ->modalHeading(fn(PrevUser $record) => $record->name)
                    ->modalSubmitAction(false)
                    ->extraModalFooterActions([
                        Tables\Actions\Action::make('editOwner')
                            ->label(__('Edit Owner'))
                            ->icon('heroicon-o-pencil-square')
                            ->url(fn(PrevUser $record) => PrevUserResource::getUrl('edit', ['record' => $record]))
                            ->openUrlInNewTab(),
                    ]),

                Tables\Actions\EditAction::make('edit-ownership')
                    ->label(__('Edit Ownership'))
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'current' => 'Current',
                                'old' => 'Old',
                                null => 'Unknown',
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
