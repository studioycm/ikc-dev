<?php

namespace App\Filament\Resources\PrevShowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrevShowArenaRelationManager extends RelationManager
{
    protected static string $relationship = 'arenas';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Arenas');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('GroupName')
                    ->required()
                    ->maxLength(255)
                    ->label(__('Name')),
                Forms\Components\TextInput::make('OrderID')
                    ->required()
                    ->numeric()
                    ->label(__('Order')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->with(['judges']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('ID'))->toggleable(),
                Tables\Columns\TextColumn::make('GroupName')->label(__('Name'))->toggleable(),
                Tables\Columns\TextColumn::make('judges.JudgeNameHE')
                    ->label(__('Judges'))
                    ->separator('; ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('OrderID')->label(__('Order'))->numeric()->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
