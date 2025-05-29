<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevBreedResource\Pages;
use App\Filament\Resources\PrevBreedResource\RelationManagers;
use App\Models\PrevColor;
use App\Models\PrevDog;
use App\Models\PrevBreed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Exports\DogExporter;
// use App\Filament\Imports\DogImporter;
// use Filament\Tables\Actions\ExportAction;
// use Filament\Tables\Actions\ImportAction;

class PrevBreedResource extends Resource
{
    protected static ?string $model = PrevBreed::class;


    public static function getModelLabel(): string
    {
        return __('Breed');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breeds');
    }

    public static function getNavigationGroup(): string
    {
        return __('Dogs Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breeds');
    }

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'fas-dna';

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
                Forms\Components\TextInput::make('BreedName')
                    ->maxLength(200),
                Forms\Components\TextInput::make('BreedCode')
                    ->numeric(),
                Forms\Components\Textarea::make('Desc')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('BreedNameEN')
                    ->maxLength(200),
                Forms\Components\TextInput::make('GroupID')
                    ->numeric(),
                Forms\Components\TextInput::make('FCICODE')
                    ->maxLength(200),
                Forms\Components\TextInput::make('UserManagerID')
                    ->numeric(),
                Forms\Components\TextInput::make('ClubManagerID')
                    ->numeric(),
                Forms\Components\TextInput::make('fci_group')
                    ->maxLength(50),
                Forms\Components\TextInput::make('status')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->withCount(['dogs']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('BreedName')
                    ->label('Hebrew Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BreedNameEN')
                    ->label('English Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BreedCode')
                    ->label('Breed Code')
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true),
                Tables\Columns\TextColumn::make('dogs_count')
                    ->label(__('Dogs Count'))
                    ->counts('dogs')
                    ->numeric()
                    ->sortable(['dogs_count'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('FCICODE')
                    ->label('FCI Code')
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true),
                Tables\Columns\TextColumn::make('fci_group')
                    ->label('FCI Group')
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('DataID')
                    ->label('Previous ID')
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GroupID')
                    ->label('Previous GroupID')
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('UserManagerID')
                    ->label('User Manager ID')
                    ->description(fn (PrevBreed $record): string => $record->userManager->FullName ?? 'n/a')
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ClubManagerID')
                    ->label('Club Manager ID')
                    ->description(fn (PrevBreed $record): string => $record->clubManager->FullName ?? 'n/a')
                    ->numeric()
                    ->sortable()
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->toggleable(isToggledHiddenByDefault: false),
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
                Filter::make('trashed')
                    ->form([
                        Forms\Components\ToggleButtons::make('trashed')
                            ->label(__('Trashed'))
                            ->options([
                                'not_deleted' => 'Not Deleted',
                                'deleted'     => 'Deleted',
                                'all'         => 'All',
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted'     => 'danger',
                                'all'         => 'gray',
                            ])
                            ->default('not_deleted')
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['trashed']) || $data['trashed'] === 'all') {
                            return $query;
                        }
                        return match ($data['trashed']) {
                            'deleted'     => $query->onlyTrashed(),
                            'not_deleted' => $query->withoutTrashed(),
                        };
                    }),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevBreeds::route('/'),
            'create' => Pages\CreatePrevBreed::route('/create'),
            'edit' => Pages\EditPrevBreed::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PrevBreedResource\Widgets\BreedStats::class,
        ];
    }
}
