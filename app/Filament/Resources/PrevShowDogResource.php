<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowDogResource\Pages;
use App\Models\PrevShowDog;
use Filament\Forms\Components\DatePicker;
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
                return $query->with(['show', 'dog', 'result']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label(__('id'))
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('show.TitleName')
                    ->label(__('Show Title'))
                    ->description(fn(PrevShowDog $record): int => (int)$record->ShowID),
                TextColumn::make('dog.full_name')
                    ->label(__('Dog name'))
                    ->description(fn(PrevShowDog $r) => ($r->SagirID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->dog ? PrevDogResource::getUrl('view', ['record' => $r->dog->getKey()]) : null)
                    ->openUrlInNewTab()
                    ->searchable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name', 'DogsDB.SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name']),

                TextColumn::make('arena_summary')
                    ->label(__('Arena name'))
                    ->state(fn(PrevShowDog $r) => $r->arena?->GroupName ?? '—')
                    ->description(fn(PrevShowDog $r) => ($r->ArenaID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->ArenaID ? PrevShowArenaResource::getUrl('view', ['record' => $r->ArenaID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('class_summary')
                    ->label(__('Class type'))
                    ->state(fn(PrevShowDog $r) => $r->showClass?->ClassName ?? '—')
                    ->description(fn(PrevShowDog $r) => ($r->ClassID ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->ClassID ? PrevShowClassResource::getUrl('view', ['record' => $r->ClassID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('breed_summary')
                    ->label(__('Breed'))
                    ->state(fn(PrevShowDog $r) => $r->breed?->BreedNameEN ?: ($r->breed?->BreedName ?: '—'))
                    ->description(fn(PrevShowDog $r) => __('Breed Code') . ': ' . ($r->breed?->BreedCode ?? '—'))
                    ->url(fn(PrevShowDog $r) => $r->ShowBreedID ? PrevShowBreedResource::getUrl('view', ['record' => $r->ShowBreedID]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('result.DataID')
                    ->label(__('Result'))
                    ->description(fn(PrevShowDog $record): int => (int)$record->result?->SagirID)
                    ->url(function ($state) {
                        return $state ? PrevShowResultResource::getUrl('edit', ['record' => $state]) : null;
                    })
                    ->toggleable(),

            ])
            ->filters([
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
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevShowDogs::route('/'),
            'create' => Pages\CreatePrevShowDog::route('/create'),
            'edit' => Pages\EditPrevShowDog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
