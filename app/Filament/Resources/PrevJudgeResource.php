<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevJudgeResource\Pages;
use App\Models\PrevJudge;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;

class PrevJudgeResource extends Resource
{
    protected static ?string $model = PrevJudge::class;

    protected static ?string $slug = 'prev-judges';

    protected static ?string $navigationIcon = 'fas-gavel';

    protected static ?int $navigationSort = 60;

    public static function getModelLabel(): string
    {
        return __('Judge');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Judges');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Judges');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([//
                TextInput::make('DataID')
                    ->disabled()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('JudgeNameHE')
                    ->required(),

                TextInput::make('JudgeNameEN')
                    ->required(),

                TextInput::make('Country'),

                TextInput::make('BreedID')
                    ->integer(),

                TextInput::make('Email'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->withCount(['arenas']);
            })
            ->columns([
                TextColumn::make('JudgeNameHE')
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->sortable()
                    ->label(__('Name Hebrew')),

                TextColumn::make('JudgeNameEN')
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->sortable()
                    ->label(__('Name English')),

                TextColumn::make('Country')
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->sortable()
                    ->label(__('Country')),

                TextColumn::make('Email')
                    ->searchable(isGlobal: false, isIndividual: true)
                    ->sortable()
                    ->label(__('Email')),

                TextColumn::make('arenas_count')
                    ->counts('arenas')
                    ->numeric()
                    ->sortable(['arenas_count'])
                    ->label(__('Arenas')),

                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('CreationDateTime')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                TextColumn::make('DataID')
                    ->numeric()
                    ->label(__('DataID'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                // arenas count filter
                Filters\Filter::make('arenas_count')
                    ->label(__('Arenas Count'))
                    ->form([
                        Components\TextInput::make('arenas_count')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->minValue(1)
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['arenas_count'],
                            fn(Builder $query, $arenas_count): Builder => $query->has('arenas', $data['operator'], $arenas_count)
                        );
                    })
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
            'index' => Pages\ListPrevJudges::route('/'),
            'create' => Pages\CreatePrevJudge::route('/create'),
            'edit' => Pages\EditPrevJudge::route('/{record}/edit'),
        ];
    }
}
