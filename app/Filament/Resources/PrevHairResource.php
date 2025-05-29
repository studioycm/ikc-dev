<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevHairResource\Pages;
use App\Filament\Resources\PrevHairResource\RelationManagers;
use App\Models\PrevHair;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevHairResource extends Resource
{
    protected static ?string $model = PrevHair::class;

    public static function getModelLabel(): string
    {
        return __('Hair Type');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Hair Types');
    }
    public static function getNavigationGroup(): string
    {
        return __('Dogs Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('Hair Types');
    }

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'fas-wind';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('DataID')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('ModificationDateTime'),
                Forms\Components\DateTimePicker::make('CreationDateTime'),
                Forms\Components\TextInput::make('HairNameHE')
                    ->maxLength(200),
                Forms\Components\TextInput::make('HairNameEN')
                    ->maxLength(200),
                Forms\Components\TextInput::make('Remark')
                    ->maxLength(4000),
                Forms\Components\TextInput::make('OldCode')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('DataID')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('HairNameHE')
                    ->searchable(),
                Tables\Columns\TextColumn::make('HairNameEN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Remark')
                    ->searchable(),
                Tables\Columns\TextColumn::make('OldCode')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListPrevHairs::route('/'),
            'create' => Pages\CreatePrevHair::route('/create'),
            'edit' => Pages\EditPrevHair::route('/{record}/edit'),
        ];
    }
}
