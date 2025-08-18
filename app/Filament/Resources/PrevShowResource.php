<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowResource\Pages;
use App\Filament\Resources\PrevShowResource\RelationManagers\PrevShowArenaRelationManager;
use App\Filament\Resources\PrevShowResource\RelationManagers\PrevShowClassRelationManager;
use App\Models\PrevShow;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PrevShowResource extends Resource
{
    protected static ?string $model = PrevShow::class;

    protected static ?string $slug = 'prev-shows';

    protected static ?string $navigationIcon = 'fas-trophy';

    protected static ?int $navigationSort = 60;

    public static function getModelLabel(): string
    {
        return __('Show');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Shows');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Shows');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('DataID')
                    ->required()
                    ->integer(),

                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('TitleName'),

                DatePicker::make('StartDate'),

                TextInput::make('ShortDesc'),

                TextInput::make('LongDesc'),

                TextInput::make('TopImage'),

                TextInput::make('MaxRegisters')
                    ->numeric(),

                TextInput::make('ShowType')
                    ->numeric(),

                TextInput::make('ClubID')
                    ->numeric(),

                DatePicker::make('EndRegistrationDate'),

                TextInput::make('ShowStatus')
                    ->numeric(),

                DatePicker::make('EndDate'),

                TextInput::make('ShowPrice')
                    ->numeric(),

                TextInput::make('Dog2Price1')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price2')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price3')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price4')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price5')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price6')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price7')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price8')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price9')
                    ->required()
                    ->numeric(),

                TextInput::make('Dog2Price10')
                    ->required()
                    ->numeric(),

                TextInput::make('CouplesPrice')
                    ->numeric(),

                TextInput::make('BGidulPrice')
                    ->numeric(),

                TextInput::make('ZezaimPrice')
                    ->numeric(),

                TextInput::make('YoungPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice')
                    ->numeric(),

                TextInput::make('MoreDogsPrice2')
                    ->numeric(),

                TextInput::make('TicketCost')
                    ->numeric(),

                TextInput::make('IsExtraTickets'),

                TextInput::make('IsParking'),

                TextInput::make('MoreTicketsSelect'),

                TextInput::make('ParkingSelect'),

                TextInput::make('PeototCost')
                    ->numeric(),

                TextInput::make('FreeTextDesc'),

                TextInput::make('start_from_index'),

                TextInput::make('location'),

                TextInput::make('Check_all_members')
                    ->required()
                    ->integer(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['club', 'arenas', 'classes'])->withCount(['registrations', 'showDogs', 'results']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('TitleName')
                    ->label(__('Title'))
                    ->description(fn(PrevShow $record): HtmlString => new HtmlString('<p><span>' . $record->club->Name . '</span><br><span>' . $record->ShowType . '</span>, <span>' . $record->location . '</span></p>'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('LongDesc')
                    ->label(__('Description'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->html()
                    ->wrap(),

                TextColumn::make('StartDate')
                    ->date()
                    ->label(__('Starting at'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('EndDate')
                    ->label(__('Ending at'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('EndRegistrationDate')
                    ->date()
                    ->label(__('Registration Ending at'))
                    ->sortable()
                    ->toggleable(),

                //                TextColumn::make('FreeTextDesc')
                //                    ->label(__('Additional Information')),

                TextColumn::make('MaxRegisters')
                    ->label(__('Max. Registrations'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('ShowStatus')
                    ->label(__('Show Status'))
                    ->boolean(fn($state): bool => $state === 2)
                    ->color(fn($state): string => $state === 2 ? 'success' : 'danger')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('arenas_count')
                    ->label(__('Arenas'))
                    ->counts('arenas')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('classes_count')
                    ->label(__('Classes'))
                    ->counts('classes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('registrations_count')
                    ->label(__('Registrations'))
                    ->counts('registrations')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('show_dogs_count')
                    ->label(__('Show Dogs'))
                    ->counts('showDogs')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('results_count')
                    ->label(__('Results'))
                    ->counts('results')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ShowPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price1')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price2')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price3')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price4')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price5')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price6')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price7')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price8')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price9')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('Dog2Price10')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('CouplesPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('BGidulPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ZezaimPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('YoungPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('MoreDogsPrice')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('MoreDogsPrice2')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('PeototCost')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('TicketCost')
                    ->money('ILS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('IsExtraTickets'),

                TextColumn::make('IsParking'),

                TextColumn::make('MoreTicketsSelect'),

                TextColumn::make('ParkingSelect'),

                TextColumn::make('start_from_index'),

                TextColumn::make('Check_all_members'),

                TextColumn::make('DataID')
                    ->numeric()
                    ->label(__('Data ID'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ModificationDateTime')
                    ->date()
                    ->label(__('Last Modified Date'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('CreationDateTime')
                    ->date()
                    ->label(__('Created Date'))
                    ->sortable()
                    ->toggleable(),

                ImageColumn::make('banner_image'),

            ])
            ->filters([
                TrashedFilter::make('trashed'),
            ])
            ->actions([
                ViewAction::make(),
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
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy(\DB::raw('YEAR(StartDate)'), 'desc')
                    ->orderBy(\DB::raw('MONTH(StartDate)'), 'desc')
                    ->orderBy('id', 'desc');
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('ShowTabs')
                    ->tabs([
                        Tab::make(__('Overview'))
                            ->schema([
                                InfolistGrid::make(3)->schema([
                                    TextEntry::make('TitleName')->label(__('Title'))->columnSpan(2),
                                    TextEntry::make('club.Name')->label(__('Club')),
                                    TextEntry::make('ShowType')->label(__('Type')),
                                    TextEntry::make('location')->label(__('Location')),
                                    IconEntry::make('ShowStatus')->label(__('Active'))->boolean(),
                                ]),
                                InfolistSection::make(__('Counts'))
                                    ->schema([
                                        TextEntry::make('arenas_count')->label(__('Arenas'))->state(fn(PrevShow $record) => $record->arenas()->count()),
                                        TextEntry::make('classes_count')->label(__('Classes'))->state(fn(PrevShow $record) => $record->classes()->count()),
                                        TextEntry::make('registrations_count')->label(__('Registrations'))->state(fn(PrevShow $record) => $record->registrations()->count()),
                                        TextEntry::make('show_dogs_count')->label(__('Show Dogs'))->state(fn(PrevShow $record) => $record->showDogs()->count()),
                                        TextEntry::make('results_count')->label(__('Results'))->state(fn(PrevShow $record) => $record->results()->count()),
                                    ])->columns(5),
                            ]),
                        Tab::make(__('Dates'))
                            ->schema([
                                InfolistGrid::make(2)->schema([
                                    TextEntry::make('StartDate')->date()->label(__('Starting at')),
                                    TextEntry::make('EndDate')->date()->label(__('Ending at')),
                                    TextEntry::make('EndRegistrationDate')->date()->label(__('Registration ends')),
                                    TextEntry::make('CreationDateTime')->since()->label(__('Created')),
                                    TextEntry::make('ModificationDateTime')->since()->label(__('Updated')),
                                ]),
                            ]),
                        Tab::make(__('Pricing'))
                            ->schema([
                                InfolistGrid::make(3)->schema([
                                    TextEntry::make('ShowPrice')->money('ILS'),
                                    TextEntry::make('Dog2Price1')->money('ILS'),
                                    TextEntry::make('Dog2Price2')->money('ILS'),
                                    TextEntry::make('Dog2Price3')->money('ILS'),
                                    TextEntry::make('Dog2Price4')->money('ILS'),
                                    TextEntry::make('Dog2Price5')->money('ILS'),
                                    TextEntry::make('Dog2Price6')->money('ILS'),
                                    TextEntry::make('Dog2Price7')->money('ILS'),
                                    TextEntry::make('Dog2Price8')->money('ILS'),
                                    TextEntry::make('Dog2Price9')->money('ILS'),
                                    TextEntry::make('Dog2Price10')->money('ILS'),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShows::route('/'),
            'create' => Pages\CreatePrevShow::route('/create'),
            'view' => Pages\ViewPrevShow::route('/{record}'),
            'edit' => Pages\EditPrevShow::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            PrevShowArenaRelationManager::class,
            PrevShowClassRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
