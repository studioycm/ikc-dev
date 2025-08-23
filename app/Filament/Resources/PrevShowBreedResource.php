<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowBreedResource\Pages;
use App\Filament\Resources\PrevShowResource as ShowRes;
use App\Models\PrevShowBreed;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
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

class PrevShowBreedResource extends Resource
{
    protected static ?string $model = PrevShowBreed::class;

    protected static ?string $slug = 'prev-show-breeds';

    protected static ?string $navigationIcon = 'fas-dna';

    protected static ?int $navigationSort = 70;

    public static function getModelLabel(): string
    {
        return __('Show Breed');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Breeds');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Breeds');
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

                TextInput::make('RaceID')
                    ->integer(),

                TextInput::make('ArenaID')
                    ->integer(),

                TextInput::make('Remarks'),

                TextInput::make('OrderID')
                    ->integer(),

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('MainArenaID')
                    ->integer(),

                TextInput::make('JudgeID')
                    ->integer(),

                TextInput::make('ArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('MainArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                TextInput::make('RaceID')
                    ->required()
                    ->integer(),

                TextInput::make('JudgeID')
                    ->required()
                    ->integer(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('show_summary')
                    ->label(__('Show title'))
                    ->state(fn(PrevShowBreed $r) => $r->show?->TitleName ?? '—')
                    ->description(fn(PrevShowBreed $r) => __('ID') . ': ' . ($r->ShowID ?? '—'))
                    ->url(fn(PrevShowBreed $r) => $r->ShowID ? ShowRes::getUrl('view', ['record' => $r->ShowID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('DataID')->label(__('ID')),
                TextColumn::make('ModificationDateTime')->date()->label(__('Modified')),
                TextColumn::make('CreationDateTime')->date()->label(__('Created')),
                TextColumn::make('arena_id')->label(__('Arena'))
                    ->state(fn(PrevShowBreed $r) => $r->ArenaID),
                TextColumn::make('judge_id')->label(__('Judge'))
                    ->state(fn(PrevShowBreed $r) => $r->JudgeID),
                TextColumn::make('order')->label(__('Order'))
                    ->state(fn(PrevShowBreed $r) => $r->OrderID),
                TextColumn::make('breed_summary')
                    ->label(__('Breed'))
                    ->state(fn(PrevShowBreed $r) => ($r->breed?->BreedNameEN ?: $r->breed?->BreedName ?: '—') . ($r->breed?->BreedCode ? ' (' . $r->breed?->BreedCode . ')' : ''))
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('breed', function ($bq) use ($search) {
                            $bq->where('BreedName', 'like', "%{$search}%")
                                ->orWhere('BreedNameEN', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(),
                TextColumn::make('Remarks')->label(__('Remarks'))->wrap()->toggleable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make('BreedTabs')->tabs([
                Tab::make(__('Overview'))
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('ShowID')->label(__('Show')),
                            TextEntry::make('ArenaID')->label(__('Arena')),
                            TextEntry::make('JudgeID')->label(__('Judge')),
                            TextEntry::make('OrderID')->label(__('Order')),
                            TextEntry::make('Remarks')->label(__('Remarks'))->columnSpanFull(),
                        ]),
                    ]),
                Tab::make(__('Breed'))
                    ->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('breed.BreedNameEN')->label(__('Breed (EN)')),
                            TextEntry::make('breed.BreedName')->label(__('Breed (HE)')),
                            TextEntry::make('breed.BreedCode')->label(__('Breed Code')),
                            TextEntry::make('breed.FCICODE')->label(__('FCI Code')),
                        ]),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowBreeds::route('/'),
            'create' => Pages\CreatePrevShowBreed::route('/create'),
            'view' => Pages\ViewPrevShowBreed::route('/{record}'),
            'edit' => Pages\EditPrevShowBreed::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
