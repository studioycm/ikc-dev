<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevColorResource\Pages;
//use App\Filament\Resources\PrevColorResource\RelationManagers;
use App\Models\PrevColor;
//use App\Models\PrevDog;
//use App\Models\PrevBreed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
//use Filament\Support\Colors\Color;
//use Illuminate\Database\Eloquent\Builder;
//use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Exports\DogExporter;
// use App\Filament\Imports\DogImporter;
// use Filament\Tables\Actions\ExportAction;
// use Filament\Tables\Actions\ImportAction;

class PrevColorResource extends Resource
{
    protected static ?string $model = PrevColor::class;

    public static function getModelLabel(): string
    {
        return __('Color');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Colors');
    }
    public static function getNavigationGroup(): string
    {
        return __('Dogs Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('Colors');
    }

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'fab-delicious';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::$model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('DataID')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('ModificationDateTime'),
                Forms\Components\DateTimePicker::make('CreationDateTime'),
                Forms\Components\TextInput::make('ColorNameHE')
                    ->maxLength(200),
                Forms\Components\TextInput::make('ColorNameEN')
                    ->maxLength(200),
                Forms\Components\TextInput::make('Remark')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('OldCode')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DataID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ColorNameHE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ColorNameEN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Remark')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OldCode')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevColors::route('/'),
            'create' => Pages\CreatePrevColor::route('/create'),
            'edit' => Pages\EditPrevColor::route('/{record}/edit'),
        ];
    }
}
