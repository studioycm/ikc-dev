<?php

namespace App\Filament\Resources\PrevBreedingResource\RelationManagers;

use App\Filament\Resources\PrevUserTaskResource;
use App\Models\PrevUserTask;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'task_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Tasks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('due_date_time', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('task_name')
                    ->label(__('Task Name'))
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('task_type')
                    ->label(__('Task Type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('managerUser.name')
                    ->label(__('Manager'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('relatedUser.name')
                    ->label(__('Related User'))
                    ->sortable(['last_name', 'first_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
                TextColumn::make('due_date_time')
                    ->label(__('Due'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_editable')
                    ->label(__('Editable'))
                    ->boolean()
                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Task'))
                    ->url(fn(PrevUserTask $record): string => PrevUserTaskResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
