<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PrevShowDogExporter;
use App\Filament\Resources\PrevShowDogResource\Pages;
use App\Models\PrevShowDog;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PrevShowDogResource extends Resource
{
    protected static ?string $model = PrevShowDog::class;

    protected static ?string $slug = 'prev-show-dogs';

    protected static ?string $navigationIcon = 'fas-dog';

    protected static ?int $navigationSort = 90;

    public static function getModelLabel(): string
    {
        return __('Show Dog');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Dogs');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Dogs');
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

                TextInput::make('SagirID')
                    ->integer(),

                TextInput::make('GlobalSagirID'),

                TextInput::make('OrderID')
                    ->integer(),

                DatePicker::make('BirthDate'),

                TextInput::make('BreedID')
                    ->integer(),

                TextInput::make('SizeID')
                    ->integer(),

                TextInput::make('GenderID')
                    ->integer(),

                TextInput::make('DogName'),

                TextInput::make('ShowRegistrationID')
                    ->integer(),

                TextInput::make('ClassID')
                    ->integer(),

                TextInput::make('OwnerName'),

                TextInput::make('OwnerMobile'),

                TextInput::make('BeitGidulName'),

                TextInput::make('HairID'),

                TextInput::make('ColorID'),

                TextInput::make('MainArenaID')
                    ->integer(),

                TextInput::make('ArenaID')
                    ->integer(),

                TextInput::make('ShowBreedID')
                    ->integer(),

                TextInput::make('MobileNumber'),

                TextInput::make('OwnerEmail'),

                TextInput::make('new_show_registration_id')
                    ->integer(),

                DatePicker::make('present'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['show', 'arena', 'showClass', 'breed', 'dog', 'prevShowResult']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('id'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('show.TitleName')
                    ->label(__('Show Title'))
                    ->searchable(['ShowsDB.id'], isIndividual: true, isGlobal: false)
                    ->description(fn(PrevShowDog $record): int => (int)$record->ShowID),

                TextColumn::make('dog.full_name')
                    ->label(__('Dog name'))
                    ->description(fn(PrevShowDog $r) => ($r->SagirID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->dog ? PrevDogResource::getUrl('view', ['record' => $r->dog->getKey()]) : null)
                    ->openUrlInNewTab()
                    ->searchable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name', 'Shows_Dogs_DB.SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name']),

                TextColumn::make('arena.GroupName')
                    ->label(__('Arena name'))
                    ->description(fn(PrevShowDog $r) => ($r->ArenaID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->ArenaID ? PrevShowArenaResource::getUrl('view', ['record' => $r->ArenaID]) : null)
                    ->openUrlInNewTab()
                    ->searchable(['Shows_Structure.id'], isIndividual: true, isGlobal: false)
                    ->toggleable(),

                TextColumn::make('showClass.ClassName')
                    ->label(__('Class type'))
                    ->description(fn(PrevShowDog $r) => ($r->ClassID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->showClass?->id ? PrevShowClassResource::getUrl('view', ['record' => $r->showClass->id]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('breed_summary')
                    ->label(__('Breed'))
                    ->state(fn(PrevShowDog $r) => $r->breed?->BreedName ?? '—')
                    ->description(fn(PrevShowDog $r) => $r->breed?->BreedNameEN ?? '—')
                    ->url(fn(PrevShowDog $r) => $r->ShowBreedID ? PrevShowBreedResource::getUrl('view', ['record' => $r->ShowBreedID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('OrderID')
                    ->label(__('Position'))
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('prevShowResult.DataID')
                    ->label(__('Result'))
                    ->color('success')
                    ->icon('fas-award')
                    ->iconPosition(IconPosition::After)
                    ->extraAttributes(fn(PrevShowDog $record) => $record->prevShowResult ? ['class' => 'cursor-pointer underline'] : [])
                    ->action(
                        Action::make('viewShowResult')
                            ->modalHeading(__('Show Result Details'))
                            ->modalWidth(MaxWidth::FiveExtraLarge) // Optional: Makes the modal a nice readable width

                            // 1. Swap the record context to the related result
                            ->record(fn(PrevShowDog $record) => $record->prevShowResult)

                            // 2. Hide the submit button so it's view-only
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel(__('Close'))
                            ->infolist(fn(Infolist $infolist, PrevShowDog $record) => PrevShowResultResource::infolist($infolist)->record($record->prevShowResult)
                            )
                            ->disabled(fn(PrevShowDog $record) => $record->prevShowResult === null)
                    )
                    ->toggleable(),

            ])
            ->filters([
                SelectFilter::make('ShowID')
                    ->label(__('Filter by Show'))
                    ->relationship('show', 'TitleName') // Connects to your show() relation
                    ->searchable()
                    ->preload(false),
                TernaryFilter::make('has_results')
                    ->label(__('Has results'))
                    ->trueLabel(__('Yes'))
                    ->falseLabel(__('No'))
                    ->queries(
                        true: fn(Builder $query) => $query->has('prevShowResult'),
                        false: fn(Builder $query) => $query->doesntHave('prevShowResult'),
                        blank: fn(Builder $query) => $query, // Returns all records when the filter is cleared
                    ),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export All'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowDogExporter::class),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
                    ->icon('fas-file-export')
                    ->color('primary')
                    ->iconPosition('after')
                    ->exporter(PrevShowDogExporter::class),
            ])
            ->defaultSort('Shows_Dogs_DB.id', 'desc')
            ->searchOnBlur()
            ->persistColumnSearchesInSession()
            ->persistSortInSession()
            ->striped()
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowDogs::route('/'),
            'create' => Pages\CreatePrevShowDog::route('/create'),
            'view' => Pages\ViewPrevShowDog::route('/{record}'),
            'edit' => Pages\EditPrevShowDog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
