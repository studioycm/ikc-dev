<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrevShowResultExporter;
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
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// use App\Filament\Resources\PrevDogResource as DogRes;
// use App\Filament\Resources\PrevShowArenaResource as ArenaRes;
// use App\Filament\Resources\PrevShowClassResource as ClassRes;
// use App\Filament\Resources\PrevShowResource as ShowRes;

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
                Grid::make(6)
                    ->schema([
                        Group::make([
                            Section::make('result_general')
                                ->schema([
                                    Group::make([
                                        Placeholder::make('DataID')
                                            ->label(__('Result ID'))
                                            ->content(fn(PrevShowResult $record): string => $record->DataID),

                                        Placeholder::make('created_at')
                                            ->label(__('Created Date'))
                                            ->content(fn(?PrevShowResult $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                        Placeholder::make('updated_at')
                                            ->label(__('Last Modified Date'))
                                            ->content(fn(?PrevShowResult $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                                        Placeholder::make('ModificationDateTime')
                                            ->label(__('Modification Date'))
                                            ->content(fn(?PrevShowResult $record): string => $record?->ModificationDateTime ?? '-'),

                                        Placeholder::make('CreationDateTime')
                                            ->label(__('Creation Date'))
                                            ->content(fn(?PrevShowResult $record): string => $record?->CreationDateTime ?? '-'),
                                    ])
                                        ->columns(5)
                                        ->columnSpanFull(),
                                    Group::make([
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
                                        ->columns(5)
                                        ->columnSpanFull(),
                                ])
                                ->heading(__('Result Details'))
                                ->columns(5),
                            Section::make('dog_info')
                                ->schema([
                                    Placeholder::make('RegDogID')
                                        ->label(__('Dog DataID'))
                                        ->content(fn(PrevShowResult $record): string => $record->RegDogID),

                                    Placeholder::make('SagirID')
                                        ->label(__('Sagir ID'))
                                        ->content(fn(PrevShowResult $record): string => $record->SagirID),

                                    Placeholder::make('dogName')
                                        ->label(__('Name'))
                                        ->content(fn(PrevShowResult $record): string => $record->dogName),

                                    Placeholder::make('breedName')
                                        ->label(__('Breed'))
                                        ->content(fn(PrevShowResult $record): string => $record->breedName),

                                    Placeholder::make('GenderID')
                                        ->label(__('Gender'))
                                        ->content(fn(PrevShowResult $record): string => $record->GenderID->getLabel()),
                                ])
                                ->heading(__('Dog Information'))
                                ->columns(5),

                            Section::make('show_info')
                                ->schema([
                                    Placeholder::make('showDog.OrderID')
                                        ->label(__('Position'))
                                        ->content(fn(PrevShowResult $record): int => $record->ShowOrderID),

                                    TextInput::make('ShowID')
                                        ->label(__('Show'))
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
                        ])
                            ->columnSpan(3),
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
                                ->columns(5),

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
                                ->columns(5),
                        ])
                            ->columnSpan(3),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistGrid::make(3)->schema([

                    // Left Column: Main Details
                    InfolistGrid::make(1)->schema([
                        InfolistSection::make(__('Dog Information'))
                            ->schema([
                                TextEntry::make('SagirID')
                                    ->label(__('Sagir ID'))
                                    ->inlineLabel(),
                                TextEntry::make('dogName')
                                    ->label(__('Name'))
                                    ->inlineLabel(),
                                TextEntry::make('breedName')
                                    ->label(__('Breed'))
                                    ->inlineLabel(),
                                TextEntry::make('GenderID')
                                    ->label(__('Gender'))
                                    ->badge()
                                    ->inlineLabel(),
                            ]),

                        InfolistSection::make(__('Show Information'))
                            ->schema([
                                TextEntry::make('show.TitleName')
                                    ->label(__('Show Name'))
                                    ->default('-')
                                    ->inlineLabel(),
                                TextEntry::make('arena.GroupName')
                                    ->label(__('Arena name'))
                                    ->default('-')
                                    ->inlineLabel(),
                                TextEntry::make('class.ClassName')
                                    ->label(__('Class type'))
                                    ->default('-')
                                    ->inlineLabel(),
                                TextEntry::make('JudgeName')
                                    ->label(__('Judge'))
                                    ->default('-')
                                    ->inlineLabel(),
                                TextEntry::make('showDog.OrderID')
                                    ->label(__('Position'))
                                    ->default('-')
                                    ->color('info')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->inlineLabel(),
                            ]),
                    ])->columnSpan(1),

                    // Right Column: Results, Titles & Meta
                    InfolistGrid::make(1)->schema([
                        InfolistSection::make(__('Awards & Ratings'))
                            ->schema([
                                TextEntry::make('Rank')
                                    ->label(__('Rank'))
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')
                                    ->columnSpan(1),

                                // Utilizing your model's excellent custom accessors
                                TextEntry::make('resultsLabels')
                                    ->label(__('Result'))
                                    ->badge()
                                    ->color('info')
                                    ->default('-')
                                    ->columnSpan(2),

                                TextEntry::make('titlesLabels')
                                    ->label(__('Titles'))
                                    ->badge()
                                    ->color('warning')
                                    ->default('-')
                                    ->columnSpan(3),

                                TextEntry::make('Remarks')
                                    ->label(__('Remarks'))
                                    ->default('-')
                                    ->columnSpanFull(),
                            ])
                            ->columns(6),

                        InfolistSection::make(__('System Data'))
                            ->schema([
                                TextEntry::make('DataID')
                                    ->label(__('Result ID')),
                                TextEntry::make('CreationDateTime')
                                    ->label(__('Created'))
                                    ->dateTime('d/m/Y H:i'),
                                TextEntry::make('ModificationDateTime')
                                    ->label(__('Modified'))
                                    ->dateTime('d/m/Y H:i'),
                            ])->columns(3),
                    ])->columnSpan(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['show', 'class', 'arena', 'showDog', 'resultDog']);
            })
            ->columns([
                TextColumn::make('DataID')
                    ->label(__('Data ID'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('show.TitleName')
                    ->label(__('Show Title'))
                    ->searchable(['ShowsDB.TitleName', 'ShowsDB.id'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevShowResult $record): int => (int)$record->ShowID)
                    ->toggleable(),
                TextColumn::make('arena.GroupName')
                    ->label(__('Arena'))
                    ->searchable(['Shows_Structure.id'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevShowResult $record): ?string => $record->MainArenaID ?? '-')
                    ->toggleable(),
                TextColumn::make('class.ClassName')
                    ->label(__('Class'))
                    ->description(fn(PrevShowResult $record): ?string => $record->ClassID ?? '-')
                    ->toggleable(),
                TextColumn::make('resultDog.fullName')
                    ->label(__('Dog'))
                    ->searchable(['SagirID'], isIndividual: true, isGlobal: true)
                    ->description(fn(PrevShowResult $record) => $record->SagirID)
                    ->toggleable(),
                TextColumn::make('GenderID')
                    ->label(__('Gender'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('resultsLabels')
                    ->label(__('Results'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('titlesLabels')
                    ->label(__('Titles'))
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('ShowID')
                    ->label(__('Filter by Show'))
                    ->relationship('show', 'TitleName') // Connects to your show() relation
                    ->searchable()
                    ->preload(false),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make()
                    ->modalWidth(MaxWidth::Full),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowResultExporter::class),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowResultExporter::class),
            ])
            ->defaultSort('DataID', 'desc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowResults::route('/'),
            'create' => Pages\CreatePrevShowResult::route('/create'),
            'view' => Pages\ViewPrevShowResult::route('/{record}'),
            'edit' => Pages\EditPrevShowResult::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
