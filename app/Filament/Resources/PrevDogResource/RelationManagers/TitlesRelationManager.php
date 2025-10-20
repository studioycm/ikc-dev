<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Filament\Resources\PrevShowResource;
use App\Models\PrevShow;
use App\Models\PrevTitle;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TitlesRelationManager extends RelationManager
{
    protected static string $relationship = 'titles';

    protected static ?string $recordTitleAttribute = 'TitleName';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Titles');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('TitleName')
            ->defaultSort('Dogs_ScoresDB.EventDate', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('TitleName')
                    ->label(__('Title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('awarding.EventPlace')
                    ->label(__('Event Place'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('awarding.EventName')
                    ->label(__('Event Name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('awarding.EventDate')
                    ->date()
                    ->label(__('Event Date')),
                Tables\Columns\TextColumn::make('awarding.ShowID')
                    ->label(__('Show ID'))
                    ->url(fn($state) => $state ? PrevShowResource::getUrl('view', ['record' => $state]) : null)
                    ->description(fn($state) => $state ? PrevShow::find($state, ['TitleName'])->TitleName : 'n/a')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('awarding.created_at')
                    ->dateTime()
                    ->label(__('Linked At')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('Attach Title'))
                    ->preloadRecordSelect()
                    ->recordSelect(function (Forms\Components\Select $select) {
                        return $select
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return PrevTitle::query()
                                    ->where('TitleName', 'like', "%{$search}%")
                                    ->orWhere('TitleCode', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($t) => [$t->TitleCode => $t->TitleName . ' (#' . $t->TitleCode . ')'])
                                    ->all();
                            })
                            ->getOptionLabelUsing(fn($value) => PrevTitle::query()->where('TitleCode', $value)->value('TitleName'));
                    })
                    ->form([
                        Forms\Components\TextInput::make('EventPlace')->label(__('Event Place'))->maxLength(255),
                        Forms\Components\TextInput::make('EventName')->label(__('Event Name'))->maxLength(255),
                        Forms\Components\DatePicker::make('EventDate')->label(__('Event Date')),
                        Forms\Components\TextInput::make('ShowID')->label(__('Show ID'))->numeric(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit Award'))
                    ->form([
                        Forms\Components\TextInput::make('EventPlace')->label(__('Event Place'))->maxLength(255),
                        Forms\Components\TextInput::make('EventName')->label(__('Event Name'))->maxLength(255),
                        Forms\Components\DatePicker::make('EventDate')->label(__('Event Date')),
                        Forms\Components\TextInput::make('ShowID')->label(__('Show ID'))->numeric(),
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
