<?php

namespace App\Filament\Resources\PrevShowResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;

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
                // Minimal for now; arenas are managed in their own resource
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->toggleable(),
                Tables\Columns\TextColumn::make('GroupName')->label('Name')->toggleable(),
                Tables\Columns\TextColumn::make('judge.JudgeNameEN')->label('Judge')->toggleable(),
                Tables\Columns\TextColumn::make('OrderID')->label('Order')->numeric()->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
