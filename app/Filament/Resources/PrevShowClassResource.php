<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowArenaResource as ArenaRes;
use App\Filament\Resources\PrevShowClassResource\Pages;
use App\Filament\Resources\PrevShowResource as ShowRes;
use App\Models\PrevShowClass;
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

class PrevShowClassResource extends Resource
{
    protected static ?string $model = PrevShowClass::class;

    protected static ?string $slug = 'prev-show-classes';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Show Class');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Classes');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Classes');
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
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['show', 'arena']))
            ->columns([
                TextColumn::make('class_summary')
                    ->label(__('Class type'))
                    ->state(fn(\App\Models\PrevShowClass $r) => $r->ClassName ?: '—')
                    ->description(fn(\App\Models\PrevShowClass $r) => __('ID') . ': ' . ($r->DataID ?? '—'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('arena_summary')
                    ->label(__('Arena name'))
                    ->state(fn(\App\Models\PrevShowClass $r) => $r->arena?->GroupName ?? '—')
                    ->description(fn(\App\Models\PrevShowClass $r) => __('ID') . ': ' . ($r->ShowArenaID ?? '—'))
                    ->url(fn(\App\Models\PrevShowClass $r) => $r->ShowArenaID ? ArenaRes::getUrl('view', ['record' => $r->ShowArenaID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('show_summary')
                    ->label(__('Show title'))
                    ->state(fn(\App\Models\PrevShowClass $r) => $r->show?->TitleName ?? '—')
                    ->description(fn(\App\Models\PrevShowClass $r) => __('ID') . ': ' . ($r->ShowID ?? '—'))
                    ->url(fn(\App\Models\PrevShowClass $r) => $r->ShowID ? ShowRes::getUrl('view', ['record' => $r->ShowID]) : null)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('ClassTabs')->tabs([
                Tab::make(__('Overview'))
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('ClassName')->label(__('Class Name')),
                            TextEntry::make('GenderID')->label(__('Gender')),
                            TextEntry::make('BreedID')->label(__('Breed (code)')),
                            TextEntry::make('ShowID')->label(__('Show ID')),
                            TextEntry::make('ShowArenaID')->label(__('Arena ID')),
                        ]),
                    ]),
                Tab::make(__('Age'))
                    ->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('Age_FromMonths')->label(__('From (months)')),
                            TextEntry::make('Age_TillMonths')->label(__('Till (months)')),
                        ]),
                    ]),
                Tab::make(__('Flags'))
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('IsChampClass'),
                            TextEntry::make('IsWorkingClass'),
                            TextEntry::make('IsOpenClass'),
                            TextEntry::make('IsVeteranClass'),
                            TextEntry::make('IsCouplesClass'),
                            TextEntry::make('IsZezaimClass'),
                            TextEntry::make('IsYoungDriverClass'),
                            TextEntry::make('IsBgidulClass'),
                        ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowClasses::route('/'),
            'create' => Pages\CreatePrevShowClass::route('/create'),
            'view' => Pages\ViewPrevShowClass::route('/{record}'),
            'edit' => Pages\EditPrevShowClass::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

}
