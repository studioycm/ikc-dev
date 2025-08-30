<?php

namespace App\Filament\Resources\PrevShowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class PrevShowArenaRelationManager extends RelationManager
{
    protected static string $relationship = 'arenas';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
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
                    ->label('Name'),
                Forms\Components\TextInput::make('OrderID')
                    ->required()
                    ->numeric()
                    ->label('Order'),
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
                Tables\Columns\TextColumn::make('id')->label('ID')->toggleable(),
                Tables\Columns\TextColumn::make('GroupName')->label('Name')->toggleable(),
                Tables\Columns\TextColumn::make('judges.JudgeNameHE')
                    ->label('Judges')
                    ->separator('; ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('OrderID')->label('Order')->numeric()->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
