<?php

namespace App\Filament\Resources\PrevShowResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'showClasses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Classes');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Minimal; classes are managed in their own resource
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->toggleable(),
                Tables\Columns\TextColumn::make('ClassID')->label('Code')->toggleable(),
                Tables\Columns\TextColumn::make('arena.GroupName')->label('Arena')->toggleable(),
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
