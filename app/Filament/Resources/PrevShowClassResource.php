<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowClassResource\Pages;
use App\Models\PrevShowClass;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevShowClassResource extends Resource
{
    protected static ?string $model = PrevShowClass::class;

    protected static ?string $slug = 'prev-show-classes';

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

                TextInput::make('ClassName'),

                TextInput::make('Age_FromMonths')
                    ->numeric(),

                TextInput::make('Age_TillMonths')
                    ->numeric(),

                TextInput::make('SpecialClassID')
                    ->numeric(),

                TextInput::make('HairID')
                    ->numeric(),

                TextInput::make('ColorID')
                    ->numeric(),

                TextInput::make('ShowRaceID')
                    ->numeric(),

                TextInput::make('ShowID')
                    ->numeric(),

                TextInput::make('ShowArenaID')
                    ->numeric(),

                TextInput::make('Remarks'),

                TextInput::make('Status')
                    ->numeric(),

                TextInput::make('OrderID')
                    ->numeric(),

                TextInput::make('IsChampClass')
                    ->numeric(),

                TextInput::make('IsWorkingClass')
                    ->numeric(),

                TextInput::make('IsOpenClass')
                    ->numeric(),

                TextInput::make('IsVeteranClass')
                    ->numeric(),

                TextInput::make('GenderID')
                    ->numeric(),

                TextInput::make('BreedID')
                    ->numeric(),

                TextInput::make('ShowMainArenaID')
                    ->numeric(),

                TextInput::make('AwardIDClass')
                    ->numeric(),

                TextInput::make('IsCouplesClass')
                    ->numeric(),

                TextInput::make('IsZezaimClass')
                    ->numeric(),

                TextInput::make('IsYoungDriverClass')
                    ->numeric(),

                TextInput::make('IsBgidulClass')
                    ->numeric(),

                TextInput::make('ShowArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowClass $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowClass $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
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

                TextColumn::make('ClassName'),

                TextColumn::make('Age_FromMonths'),

                TextColumn::make('Age_TillMonths'),

                TextColumn::make('SpecialClassID'),

                TextColumn::make('HairID'),

                TextColumn::make('ColorID'),

                TextColumn::make('ShowRaceID'),

                TextColumn::make('ShowID'),

                TextColumn::make('ShowArenaID'),

                TextColumn::make('Remarks'),

                TextColumn::make('Status'),

                TextColumn::make('OrderID'),

                TextColumn::make('IsChampClass'),

                TextColumn::make('IsWorkingClass'),

                TextColumn::make('IsOpenClass'),

                TextColumn::make('IsVeteranClass'),

                TextColumn::make('GenderID'),

                TextColumn::make('BreedID'),

                TextColumn::make('ShowMainArenaID'),

                TextColumn::make('AwardIDClass'),

                TextColumn::make('IsCouplesClass'),

                TextColumn::make('IsZezaimClass'),

                TextColumn::make('IsYoungDriverClass'),

                TextColumn::make('IsBgidulClass'),

                TextColumn::make('ShowArenaID'),

                TextColumn::make('ShowID'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowClasses::route('/'),
            'create' => Pages\CreatePrevShowClass::route('/create'),
            'edit' => Pages\EditPrevShowClass::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
