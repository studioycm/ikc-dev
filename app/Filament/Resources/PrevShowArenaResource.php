<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowArenaResource\Pages;
use App\Filament\Resources\PrevShowResource as ShowRes;
use App\Models\PrevShowArena;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
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
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrevShowArenaResource extends Resource
{
    protected static ?string $model = PrevShowArena::class;

    protected static ?string $slug = 'prev-show-arenas';

    protected static ?string $navigationIcon = 'fas-border-all';

    protected static ?int $navigationSort = 61;

    public static function getModelLabel(): string
    {
        return 'Show Arena';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Show Arenas';
    }

    public static function getNavigationGroup(): string
    {
        return 'Shows Management';
    }

    public static function getNavigationLabel(): string
    {
        return 'Arenas';
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
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('DataID')->label('ID'),
                            TextEntry::make('GroupName')->label(__('Name')),
                            TextEntry::make('ArenaType')->label(__('Type')),
                            TextEntry::make('ShowID')->label(__('Show ID')),
                            TextEntry::make('JudgeID')->label(__('Judge ID')),
                            TextEntry::make('OrderID')->label(__('Order')),
                        ]),
                    ]),
                Tab::make(__('Timing'))
                    ->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('arena_date')->date()->label(__('Arena date')),
                            TextEntry::make('OrderTime')->date()->label(__('Order time')),
                            TextEntry::make('CreationDateTime')->since()->label(__('Created')),
                            TextEntry::make('ModificationDateTime')->since()->label(__('Updated')),
                        ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['show']))
            ->columns([
                TextColumn::make('arena_summary')
                    ->label(__('Arena name'))
                    ->state(fn(PrevShowArena $r) => $r->GroupName ?: '—')
                    ->description(fn(PrevShowArena $r) => __('ID') . ': ' . ($r->DataID ?? '—'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('show_summary')
                    ->label(__('Show title'))
                    ->state(fn(PrevShowArena $r) => $r->show?->TitleName ?? '—')
                    ->description(fn(PrevShowArena $r) => ($r->ShowID ?? '—'))
                    ->url(fn(PrevShowArena $r) => $r->ShowID ? ShowRes::getUrl('view', ['record' => $r->ShowID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
            ])
            ->filters([
                TrashedFilter::make(),
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
            ]);
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
