<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowResultResource\Pages;
use App\Models\PrevShowResult;
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

class PrevShowResultResource extends Resource
{
    protected static ?string $model = PrevShowResult::class;

    protected static ?string $slug = 'prev-show-results';

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

                TextInput::make('RegDogID')
                    ->integer(),

                TextInput::make('SagirID')
                    ->integer(),

                TextInput::make('JudgeName'),

                TextInput::make('ShowOrderID')
                    ->integer(),

                TextInput::make('MainArenaID')
                    ->integer(),

                TextInput::make('SubArenaID')
                    ->integer(),

                TextInput::make('ClassID')
                    ->integer(),

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('JCAC')
                    ->integer(),

                TextInput::make('GCAC')
                    ->integer(),

                TextInput::make('REJCAC')
                    ->integer(),

                TextInput::make('REGCAC')
                    ->integer(),

                TextInput::make('CW')
                    ->integer(),

                TextInput::make('BJ')
                    ->integer(),

                TextInput::make('BV')
                    ->integer(),

                TextInput::make('CAC')
                    ->integer(),

                TextInput::make('RECACIB')
                    ->integer(),

                TextInput::make('RECAC')
                    ->integer(),

                TextInput::make('BP')
                    ->integer(),

                TextInput::make('BB')
                    ->integer(),

                TextInput::make('BOB')
                    ->integer(),

                TextInput::make('Excellent')
                    ->integer(),

                TextInput::make('Cannotbejudged')
                    ->integer(),

                TextInput::make('VeryGood')
                    ->integer(),

                TextInput::make('VeryPromising')
                    ->integer(),

                TextInput::make('Good')
                    ->integer(),

                TextInput::make('Promising')
                    ->integer(),

                TextInput::make('Sufficient')
                    ->integer(),

                TextInput::make('Satisfactory')
                    ->integer(),

                TextInput::make('Disqualified')
                    ->integer(),

                TextInput::make('Remarks'),

                TextInput::make('Rank')
                    ->integer(),

                TextInput::make('CACIB')
                    ->integer(),

                TextInput::make('BD')
                    ->integer(),

                TextInput::make('BOS')
                    ->integer(),

                TextInput::make('BPIS')
                    ->integer(),

                TextInput::make('BPIS2')
                    ->integer(),

                TextInput::make('BPIS3')
                    ->integer(),

                TextInput::make('BJIS')
                    ->integer(),

                TextInput::make('BJIS2')
                    ->integer(),

                TextInput::make('BJIS3')
                    ->integer(),

                TextInput::make('BVIS')
                    ->integer(),

                TextInput::make('BVIS2')
                    ->integer(),

                TextInput::make('BVIS3')
                    ->integer(),

                TextInput::make('BIG')
                    ->integer(),

                TextInput::make('BIG2')
                    ->integer(),

                TextInput::make('BIG3')
                    ->integer(),

                TextInput::make('BIS')
                    ->integer(),

                TextInput::make('BIS2')
                    ->integer(),

                TextInput::make('BIS3')
                    ->integer(),

                TextInput::make('BreedID')
                    ->integer(),

                TextInput::make('NotPresent')
                    ->integer(),

                TextInput::make('GenderID')
                    ->integer(),

                TextInput::make('NoTitle')
                    ->integer(),

                TextInput::make('VCAC')
                    ->integer(),

                TextInput::make('RVCAC')
                    ->integer(),

                TextInput::make('BBaby')
                    ->integer(),

                TextInput::make('BBIS')
                    ->integer(),

                TextInput::make('BBIS2')
                    ->integer(),

                TextInput::make('BBIS3')
                    ->integer(),

                TextInput::make('BBaby2')
                    ->integer(),

                TextInput::make('BBaby3')
                    ->integer(),

                TextInput::make('VCACIB')
                    ->integer(),

                TextInput::make('JCACIB')
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowResult $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowResult $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
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

                TextColumn::make('RegDogID'),

                TextColumn::make('SagirID'),

                TextColumn::make('JudgeName'),

                TextColumn::make('ShowOrderID'),

                TextColumn::make('MainArenaID'),

                TextColumn::make('SubArenaID'),

                TextColumn::make('ClassID'),

                TextColumn::make('ShowID'),

                TextColumn::make('JCAC'),

                TextColumn::make('GCAC'),

                TextColumn::make('REJCAC'),

                TextColumn::make('REGCAC'),

                TextColumn::make('CW'),

                TextColumn::make('BJ'),

                TextColumn::make('BV'),

                TextColumn::make('CAC'),

                TextColumn::make('RECACIB'),

                TextColumn::make('RECAC'),

                TextColumn::make('BP'),

                TextColumn::make('BB'),

                TextColumn::make('BOB'),

                TextColumn::make('Excellent'),

                TextColumn::make('Cannotbejudged'),

                TextColumn::make('VeryGood'),

                TextColumn::make('VeryPromising'),

                TextColumn::make('Good'),

                TextColumn::make('Promising'),

                TextColumn::make('Sufficient'),

                TextColumn::make('Satisfactory'),

                TextColumn::make('Disqualified'),

                TextColumn::make('Remarks'),

                TextColumn::make('Rank'),

                TextColumn::make('CACIB'),

                TextColumn::make('BD'),

                TextColumn::make('BOS'),

                TextColumn::make('BPIS'),

                TextColumn::make('BPIS2'),

                TextColumn::make('BPIS3'),

                TextColumn::make('BJIS'),

                TextColumn::make('BJIS2'),

                TextColumn::make('BJIS3'),

                TextColumn::make('BVIS'),

                TextColumn::make('BVIS2'),

                TextColumn::make('BVIS3'),

                TextColumn::make('BIG'),

                TextColumn::make('BIG2'),

                TextColumn::make('BIG3'),

                TextColumn::make('BIS'),

                TextColumn::make('BIS2'),

                TextColumn::make('BIS3'),

                TextColumn::make('BreedID'),

                TextColumn::make('NotPresent'),

                TextColumn::make('GenderID'),

                TextColumn::make('NoTitle'),

                TextColumn::make('VCAC'),

                TextColumn::make('RVCAC'),

                TextColumn::make('BBaby'),

                TextColumn::make('BBIS'),

                TextColumn::make('BBIS2'),

                TextColumn::make('BBIS3'),

                TextColumn::make('BBaby2'),

                TextColumn::make('BBaby3'),

                TextColumn::make('VCACIB'),

                TextColumn::make('JCACIB'),
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
            'index' => Pages\ListPrevShowResults::route('/'),
            'create' => Pages\CreatePrevShowResult::route('/create'),
            'edit' => Pages\EditPrevShowResult::route('/{record}/edit'),
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
