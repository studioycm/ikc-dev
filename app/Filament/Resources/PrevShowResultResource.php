<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevDogResource as DogRes;
use App\Filament\Resources\PrevShowArenaResource as ArenaRes;
use App\Filament\Resources\PrevShowClassResource as ClassRes;
use App\Filament\Resources\PrevShowResource as ShowRes;
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

    protected static ?int $navigationSort = 60;

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
            ->modifyQueryUsing(fn(Builder $q) => $q->with(['dog', 'show', 'mainArena', 'subArena', 'class', 'breed']))
            ->columns([
                TextColumn::make('dog_summary')
                    ->label(__('Dog name'))
                    ->state(fn(\App\Models\PrevShowResult $r) => $r->dog?->full_name ?? '—')
                    ->description(fn(\App\Models\PrevShowResult $r) => __('Sagir ID') . ': ' . ($r->SagirID ?? '—'))
                    ->url(fn(\App\Models\PrevShowResult $r) => $r->dog ? DogRes::getUrl('view', ['record' => $r->dog->getKey()]) : null)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('show_summary')
                    ->label(__('Show title'))
                    ->state(fn(\App\Models\PrevShowResult $r) => $r->show?->TitleName ?? '—')
                    ->description(fn(\App\Models\PrevShowResult $r) => __('ID') . ': ' . ($r->ShowID ?? '—'))
                    ->url(fn(\App\Models\PrevShowResult $r) => $r->ShowID ? ShowRes::getUrl('view', ['record' => $r->ShowID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('arena_summary')
                    ->label(__('Arena name'))
                    ->state(fn(\App\Models\PrevShowResult $r) => $r->mainArena?->GroupName ?? '—')
                    ->description(fn(\App\Models\PrevShowResult $r) => __('ID') . ': ' . ($r->MainArenaID ?? '—'))
                    ->url(fn(\App\Models\PrevShowResult $r) => $r->MainArenaID ? ArenaRes::getUrl('view', ['record' => $r->MainArenaID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('class_summary')
                    ->label(__('Class type'))
                    ->state(fn(\App\Models\PrevShowResult $r) => $r->class?->ClassName ?? '—')
                    ->description(fn(\App\Models\PrevShowResult $r) => __('ID') . ': ' . ($r->ClassID ?? '—'))
                    ->url(fn(\App\Models\PrevShowResult $r) => $r->ClassID ? ClassRes::getUrl('view', ['record' => $r->ClassID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('breed_summary')
                    ->label(__('Breed'))
                    ->state(fn(\App\Models\PrevShowResult $r) => $r->breed?->BreedNameEN ?: ($r->breed?->BreedName ?: '—'))
                    ->description(fn(\App\Models\PrevShowResult $r) => __('Breed Code') . ': ' . ($r->breed?->BreedCode ?? '—')),
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
}
