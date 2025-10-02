<?php

namespace App\Filament\Resources;

// use App\Filament\Resources\PrevDogResource as DogRes;
// use App\Filament\Resources\PrevShowArenaResource as ArenaRes;
// use App\Filament\Resources\PrevShowClassResource as ClassRes;
// use App\Filament\Resources\PrevShowResource as ShowRes;
use App\Filament\Resources\PrevShowResultResource\Pages;
use App\Models\PrevShowResult;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrevShowResultResource extends Resource
{
    protected static ?string $model = PrevShowResult::class;

    protected static ?string $slug = 'prev-show-results';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 100;

    public static function getModelLabel(): string
    {
        return __('Show Result');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Results');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Results');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Group::make([
                            Section::make('dog_info')
                                ->schema([
                                    TextInput::make('DataID')
                                        ->label(__('Result ID'))
                                        ->disabled()
                                        ->unique()
                                        ->integer(),

                                    TextInput::make('RegDogID')
                                        ->label(__('Dog DataID'))
                                        ->integer(),

                                    TextInput::make('SagirID')
                                        ->label(__('Sagir ID'))
                                        ->integer(),

                                    TextInput::make('BreedID')
                                        ->label(__('Breed ID'))
                                        ->integer(),

                                    TextInput::make('GenderID')
                                        ->label(__('Gender'))
                                        ->integer(),
                                ])
                                ->heading(__('Dog Information'))
                                ->columns(5),

                            Section::make('show_info')
                                ->schema([
                                    Placeholder::make('created_at')
                                        ->label('Created Date')
                                        ->content(fn(?PrevShowResult $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                    Placeholder::make('updated_at')
                                        ->label('Last Modified Date')
                                        ->content(fn(?PrevShowResult $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                                    Placeholder::make('ModificationDateTime')
                                        ->label(__('Modification Date'))
                                        ->content(fn(?PrevShowResult $record): string => $record?->ModificationDateTime ?? '-'),

                                    Placeholder::make('CreationDateTime')
                                        ->label(__('Creation Date'))
                                        ->content(fn(?PrevShowResult $record): string => $record?->CreationDateTime ?? '-'),

                                    TextInput::make('ShowOrderID')
                                        ->label(__('Position'))
                                        ->integer(),

                                    TextInput::make('ShowID')
                                        ->label(__('Show ID'))
                                        ->integer(),

                                    TextInput::make('MainArenaID')
                                        ->label(__('Arena ID'))
                                        ->integer(),

                                    TextInput::make('ClassID')
                                        ->label(__('Class ID'))
                                        ->integer(),


                                    TextInput::make('JudgeName')
                                        ->label(__('Judge')),
                                ])
                                ->heading(__('Show Information'))
                                ->columns(5),
                            Section::make('result_general')
                                ->schema([
                                    TextInput::make('Rank')
                                        ->label(__('Rank'))
                                        ->integer()
                                        ->maxValue(99)
                                        ->columnSpan(1),
                                    Textarea::make('Remarks')
                                        ->label(__('Remarks'))
                                        ->rows(3)
                                        ->autosize()
                                        ->columnSpan(4),
                                ])
                                ->heading(__('Result Details'))
                                ->columns(5),
                        ]),
                        Group::make([
                            Section::make('result_options')
                                ->schema([
                                    Toggle::make('Excellent')
                                        ->label(__('Excellent'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('VeryGood')
                                        ->label(__('Very Good'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('VeryPromising')
                                        ->label(__('Very Promising'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Good')
                                        ->label(__('Good'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Promising')
                                        ->label(__('Promising'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Sufficient')
                                        ->label(__('Sufficient'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Satisfactory')
                                        ->label(__('Satisfactory'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Cannotbejudged')
                                        ->label(__('Cannot Be Judged'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('Disqualified')
                                        ->label(__('Disqualified'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('NotPresent')
                                        ->label(__('Not Present'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('NoTitle')
                                        ->label(__('No Title'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),
                                ])
                                ->heading(__('Result Options'))
                                ->columns(4),

                            Section::make('titles_awards')
                                ->schema([
                                    Toggle::make('JCAC')
                                        ->label(__('JCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('REJCAC')
                                        ->label(__('REJCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('GCAC')
                                        ->label(__('GCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),


                                    Toggle::make('REGCAC')
                                        ->label(__('REGCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('CAC')
                                        ->label(__('CAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('RECAC')
                                        ->label(__('RECAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('VCAC')
                                        ->label(__('VCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('RVCAC')
                                        ->label(__('RVCAC'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('JCACIB')
                                        ->label(__('JCACIB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('CACIB')
                                        ->label(__('CACIB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('RECACIB')
                                        ->label(__('RECACIB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('VCACIB')
                                        ->label(__('VCACIB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBaby')
                                        ->label(__('BBaby'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBaby2')
                                        ->label(__('BBaby 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBaby3')
                                        ->label(__('BBaby 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BJ')
                                        ->label(__('BJ'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BP')
                                        ->label(__('BP'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BV')
                                        ->label(__('BV'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BB')
                                        ->label(__('BB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BD')
                                        ->label(__('BD'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BOB')
                                        ->label(__('BOB'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BOS')
                                        ->label(__('BOS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBIS')
                                        ->label(__('BBIS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBIS2')
                                        ->label(__('BBIS 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BBIS3')
                                        ->label(__('BBIS 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BPIS')
                                        ->label(__('BPIS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BPIS2')
                                        ->label(__('BPIS 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BPIS3')
                                        ->label(__('BPIS 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BJIS')
                                        ->label(__('BJIS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BJIS2')
                                        ->label(__('BJIS 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BJIS3')
                                        ->label(__('BJIS 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIS')
                                        ->label(__('BIS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIS2')
                                        ->label(__('BIS 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIS3')
                                        ->label(__('BIS 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BVIS')
                                        ->label(__('BVIS'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BVIS2')
                                        ->label(__('BVIS 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BVIS3')
                                        ->label(__('BVIS 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIG')
                                        ->label(__('BIG'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIG2')
                                        ->label(__('BIG 2'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('BIG3')
                                        ->label(__('BIG 3'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),

                                    Toggle::make('CW')
                                        ->label(__('CW'))
                                        ->inline()
                                        ->onColor('success')
                                        ->offColor(null),
                                ])
                                ->heading(__('Titles & Awards'))
                                ->columns(4),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['show', 'showDog']);
            })
            ->columns([
                TextColumn::make('DataID')
                    ->label(__('Data ID'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('show.TitleName')
                    ->label(__('Show Title'))
                    ->searchable(['ShowsDB.TitleName', 'ShowsDB.id'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevShowResult $record): int => (int)$record->ShowID),
                TextColumn::make('showDog.SagirID')
                    ->label(__('Dog Name'))
                    ->description(fn(PrevShowResult $record) => $record->showDog?->dog_name),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('DataID', 'desc');
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
        return parent::getEloquentQuery();
    }
}
