<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowBreedResource\Pages;
use App\Models\PrevShowBreed;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrevShowBreedResource extends Resource
{
    protected static ?string $model = PrevShowBreed::class;

    protected static ?string $slug = 'prev-show-breeds';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('RaceID')
                    ->integer(),

                TextInput::make('ArenaID')
                    ->integer(),

                TextInput::make('Remarks'),

                TextInput::make('OrderID')
                    ->integer(),

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('MainArenaID')
                    ->integer(),

                TextInput::make('JudgeID')
                    ->integer(),

                TextInput::make('ArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('MainArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                TextInput::make('RaceID')
                    ->required()
                    ->integer(),

                TextInput::make('JudgeID')
                    ->required()
                    ->integer(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('RaceID'),

                TextColumn::make('ArenaID'),

                TextColumn::make('Remarks'),

                TextColumn::make('OrderID'),

                TextColumn::make('ShowID'),

                TextColumn::make('MainArenaID'),

                TextColumn::make('JudgeID'),

                TextColumn::make('ArenaID'),

                TextColumn::make('MainArenaID'),

                TextColumn::make('ShowID'),

                TextColumn::make('RaceID'),

                TextColumn::make('JudgeID'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowBreeds::route('/'),
            'create' => Pages\CreatePrevShowBreed::route('/create'),
            'edit' => Pages\EditPrevShowBreed::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
