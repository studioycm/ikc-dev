<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowArenaResource\Pages;
use App\Models\PrevShowArena;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrevShowArenaResource extends Resource
{
    protected static ?string $model = PrevShowArena::class;

    protected static ?string $slug = 'prev-show-arenas';

    protected static ?string $navigationIcon = 'fas-border-all';

    protected static ?int $navigationSort = 60;

    public static function getModelLabel(): string
    {
        return __('Show Arena');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Arenas');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Arenas');
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

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('GroupName'),

                TextInput::make('GroupParentID')
                    ->integer(),

                TextInput::make('ClassID')
                    ->integer(),

                TextInput::make('OrderID')
                    ->integer(),

                TextInput::make('ArenaType')
                    ->integer(),

                TextInput::make('ManagerPass'),

                TextInput::make('JudgeID')
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowArena $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowArena $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('ArenaTabs')->tabs([
                Tab::make(__('Overview'))
                    ->schema([
                        InfolistGrid::make(4)->schema([
                            TextEntry::make('show.id')->label('Show ID'),
                            TextEntry::make('show.TitleName')->label(__('Show title')),
                            TextEntry::make('show.StartDate')->date()->label(__('Show start date')),
                            TextEntry::make('show.EndDate')->date()->label(__('Show end date')),
                            TextEntry::make('show.location')->label(__('Show location')),
                        ]),
                        InfolistGrid::make(4)->schema([
                            TextEntry::make('id')->label('ID'),
                            TextEntry::make('GroupName')->label(__('Name')),
                            TextEntry::make('ArenaType')->label(__('Type')),
                            TextEntry::make('judges')
                                ->formatStateUsing(fn(PrevShowArena $r) => $r->judges->pluck('JudgeNameHE')->unique()->sort()->join(', '))
                                ->label(__('Judges')),
                            TextEntry::make('OrderID')->label(__('Position')),
                        ]),
                        InfolistGrid::make(4)->schema([
                            TextEntry::make('arena_date')->date()->label(__('Arena date')),
                            TextEntry::make('OrderTime')->date()->label(__('Order time')),
                            TextEntry::make('created_at')->since()->label(__('Created')),
                            TextEntry::make('updated_at')->since()->label(__('Updated')),
                        ]),
                    ]),
                Tab::make(__('Arena Dogs'))
                    ->schema([
                        RepeatableEntry::make('show_dogs')
                            ->schema([
                                TextEntry::make('OrderID')->label(__('Position'))->numeric(decimalPlaces: 0, thousandsSeparator: false),
                                TextEntry::make('SagirID')->label(__('Sagir'))->numeric(decimalPlaces: 0, thousandsSeparator: false),
                                TextEntry::make('BirthDate')->label(__('B.Date'))->since()->dateTooltip(),
                                TextEntry::make('hebDogName')->label(__('Name'))->columnSpan(2),
                                TextEntry::make('engDogName')->label(__('English Name'))->columnSpan(2),
                            ])
                            ->label(__('Dogs in Arena') . ': ' . $infolist->getRecord()->GroupName)
                            ->columns(4)
                            ->grid(4),
                    ])
                    ->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['show', 'judges', 'show_dogs']);
            })
            ->columns([
                TextColumn::make('show.TitleName')
                    ->label(__('Show title'))
                    ->description(fn(PrevShowArena $r) => ($r->ShowID ?? '—'))
                    ->sortable(['ShowsDB.id', 'Shows_Structure.id'])
                    ->toggleable(),
                TextColumn::make('GroupName')
                    ->label(__('Arena name'))
                    ->description(fn(PrevShowArena $r) => $r->id)
                    ->searchable(isGlobal: false, isIndividual: true, query: fn(Builder $query, string $search): Builder => $query->where('Shows_Structure.id', 'like', "%{$search}%"))
                    ->sortable(['id']),
                TextColumn::make('judges')
                    ->formatStateUsing(fn(PrevShowArena $r) => $r->judges->pluck('JudgeNameHE')->unique()->sort()->join(', '))
                    ->label(__('Judges'))
                    ->toggleable(),
                TextColumn::make('show_dogs_count')
                    ->label(__('Dogs'))
                    ->counts('show_dogs')
                    ->sortable(['show_dogs_count'])
                    ->toggleable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowArenas::route('/'),
            'create' => Pages\CreatePrevShowArena::route('/create'),
            'view' => Pages\ViewPrevShowArena::route('/{record}'),
            'edit' => Pages\EditPrevShowArena::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
