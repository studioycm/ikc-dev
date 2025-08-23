<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevJudgeResource\Pages;
use App\Models\PrevJudge;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PrevJudgeResource extends Resource
{
    protected static ?string $model = PrevJudge::class;

    protected static ?string $slug = 'prev-judges';

    protected static ?string $navigationIcon = 'fas-gavel';

    protected static ?int $navigationSort = 10;

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
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['judgedBreedsWithDogs'])
                    ->withCount([
                        'arenas',
                        'showBreeds as breeds_count' => function (Builder $q) {
                            $q->select(DB::raw('COUNT(DISTINCT Shows_Breeds.RaceID)'))
                                ->whereExists(function ($ex) {
                                    $ex->select(DB::raw(1))
                                        ->from('Shows_Dogs_DB as sd')
                                        ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                        ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                        ->whereNull('sd.deleted_at');
                                });
                        },
                    ]);
            })
            ->columns([
                TextColumn::make('JudgeNameHE')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Name Hebrew')),

                TextColumn::make('JudgeNameEN')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Name English')),

                TextColumn::make('Country')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Country')),

                TextColumn::make('Email')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label(__('Email'))
                    ->toggleable(),

                TextColumn::make('arenas_count')
                    ->counts('arenas')
                    ->numeric()
                    ->sortable(['arenas_count'])
                    ->label(__('Arenas')),
                TextColumn::make('breeds_count')
                    ->numeric()
                    ->sortable(['breeds_count'])
                    ->label(__('Breeds')),

                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('CreationDateTime')
                    ->date()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('DataID')
                    ->numeric()
                    ->label(__('DataID'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

            ])
            ->actions([
                ViewAction::make()
                    // Return a custom-loaded record for the modal (with eager loads and counts)
                    ->record(function (Model $record): Model {
                        /** @var PrevJudge $record */
                        return PrevJudge::query()
                            ->with(['judgedBreedsWithDogs'])
                            ->withCount([
                                'arenas',
                                'showBreeds as breeds_count' => function (Builder $q) {
                                    $q->select(DB::raw('COUNT(DISTINCT Shows_Breeds.RaceID)'))
                                        ->whereExists(function ($ex) {
                                            $ex->select(DB::raw(1))
                                                ->from('Shows_Dogs_DB as sd')
                                                ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                                ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                                ->whereNull('sd.deleted_at');
                                        });
                                },
                            ])
                            ->findOrFail($record->getKey());
                    })
                    // Build the modal’s infolist
                    ->infolist(function (Infolist $infolist): Infolist {
                        return $infolist->schema([
                            Tabs::make('Judge Record')->tabs([
                                Tab::make('General')->schema([
                                    InfolistGrid::make(4)->schema([
                                        TextEntry::make('DataID')->label(__('DataID')),
                                        TextEntry::make('JudgeNameHE')->label(__('Name Hebrew')),
                                        TextEntry::make('JudgeNameEN')->label(__('Name English')),
                                        TextEntry::make('Country')->label(__('Country')),
                                    ]),
                                    InfolistGrid::make(4)->schema([
                                        TextEntry::make('Email')->label(__('Email')),
                                        TextEntry::make('arenas_count')->label(__('Arenas')),
                                        TextEntry::make('breeds_count')->label(__('Breeds')),
                                        TextEntry::make('breeds_names_he')->label(__('Breeds (HE)')),
                                        TextEntry::make('breeds_names_en')->label(__('Breeds (EN)')),
                                    ]),
                                ])->label('General'),
                                Tab::make('Metadata')->schema([
                                    InfolistGrid::make(4)->schema([
                                        TextEntry::make('CreationDateTime')->label(__('Created On'))->date(),
                                        TextEntry::make('ModificationDateTime')->label(__('Modified On'))->date(),
                                    ]),
                                ])->label('Metadata'),
                            ])->columnSpanFull(),
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function infolist(Infolist $infolist): Infolist
    {

        return $infolist->schema([
            Tabs::make('Judge Record')->tabs([
                Tab::make('General')->schema([
                    InfolistGrid::make(4)->schema([
                        TextEntry::make('DataID')->label(__('DataID')),
                        TextEntry::make('JudgeNameHE')->label(__('Name Hebrew')),
                        TextEntry::make('JudgeNameEN')->label(__('Name English')),
                        TextEntry::make('Country')->label(__('Country')),
                    ]),
                    InfolistGrid::make(4)->schema([
                        TextEntry::make('Email')->label(__('Email')),
                        TextEntry::make('arenas_count')->label(__('Arenas')),
                        TextEntry::make('breeds_count')->label(__('Breeds')),
                        TextEntry::make('breeds_names_he')->label(__('Breeds (HE)')),
                        TextEntry::make('breeds_names_en')->label(__('Breeds (EN)')),
                    ]),
                ])->label('General'),
                Tab::make('Metadata')->schema([
                    InfolistGrid::make(4)->schema([
                        TextEntry::make('CreationDateTime')->label(__('Created On'))->date(),
                        TextEntry::make('ModificationDateTime')->label(__('Modified On'))->date(),
                    ]),
                ])->label('Metadata'),
            ])->columnSpanFull(),
            //            Tabs::make('Judge Record')->tabs([
            //                Tab::make('General')->schema([
            //                    InfolistGrid::make(4)->schema([
            //                        TextEntry::make('DataID')->label(__('DataID')),
            //                        TextEntry::make('JudgeNameHE')->label(__('Name Hebrew')),
            //                        TextEntry::make('JudgeNameEN')->label(__('Name English')),
            //                        TextEntry::make('Country')->label(__('Country')),
            //                    ]),
            //                    InfolistGrid::make(4)->schema([
            //                        TextEntry::make('Email')->label(__('Email')),
            //                        TextEntry::make('arenas_count')->label(__('Arenas')),
            //
            //                        // breeds_count computed from judgedBreeds
            //                        TextEntry::make('breeds_count')->label(__('Breeds')),
            //
            //                        // Names lists
            //                        TextEntry::make('breeds_names_he')->label(__('Breeds (HE)')),
            //                        TextEntry::make('breeds_names_en')->label(__('Breeds (EN)')),
            //                    ]),
            //
            //                ])->label('General'),
            //                Tab::make('Metadata')->schema([
            //                    InfolistGrid::make(4)->schema([
            //                        TextEntry::make('CreationDateTime')->label(__('Created On'))->date(),
            //                        TextEntry::make('ModificationDateTime')->label(__('Modified On'))->date(),
            //                    ]),
            //                ])->label('Metadata'),
            //            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevJudges::route('/'),
            'create' => Pages\CreatePrevJudge::route('/create'),
            'view' => Pages\ViewPrevJudge::route('/{record}'),
            'edit' => Pages\EditPrevJudge::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Index page logic is already set in table->modifyQueryUsing
        // return parent::getEloquentQuery();

        // Index page query: keep counts consistent with modal
        return parent::getEloquentQuery()
            ->with(['judgedBreedsWithDogs'])
            ->withCount([
                'arenas',
                'showBreeds as breeds_count' => function (Builder $q) {
                    $q->select(DB::raw(
                        'COUNT(DISTINCT Shows_Breeds.RaceID)'
                    ))
                        ->whereExists(function ($ex) {
                            $ex->select(DB::raw(1))
                                ->from('Shows_Dogs_DB as sd')
                                ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                                ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                                ->whereNull('sd.deleted_at');
                        });
                },
            ]);
    }
}
