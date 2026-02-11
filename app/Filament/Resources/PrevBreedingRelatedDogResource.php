<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevBreedingRelatedDogResource\Pages;
use App\Models\PrevBreedingRelatedDog;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevBreedingRelatedDogResource extends Resource
{
    protected static ?string $model = PrevBreedingRelatedDog::class;

    protected static ?string $slug = 'breeding-related-dogs';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Litter Puppy');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Litter Puppies');
    }

    public static function getNavigationGroup(): string
    {
        return __('Breedings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Litter Puppies');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('temparory_name'),

                TextInput::make('chip_number'),

                TextInput::make('sagir_id')
                    ->integer(),

                Select::make('color')
                    ->label(__('Color'))
                    ->relationship('colorName', 'ColorNameHE'),

                TextInput::make('other_color'),

                TextInput::make('gender'),

                TextInput::make('approval_status'),

                TextInput::make('is_dead')
                    ->integer(),

                TextInput::make('mother_sagir_id')
                    ->integer(),

                TextInput::make('breeding_id')
                    ->integer(),

                TextInput::make('note'),

                TextInput::make('supervisor_notes'),

                TextInput::make('document'),

                TextInput::make('updated_by')
                    ->integer(),

                TextInput::make('hair')
                    ->numeric(),

                Checkbox::make('is_submit'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevBreedingRelatedDog $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevBreedingRelatedDog $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['breeding', 'dog', 'mother', 'colorName', 'updater']);
            })
            ->columns([
                TextColumn::make('temparory_name')
                    ->label(__('Temporary Name'))
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('chip_number')
                    ->label(__('Chip'))
                    ->searchable(isIndividual: true, isGlobal: false),

                TextColumn::make('dog.full_name')
                    ->label(__('Registered Dog'))
                    ->searchable(['SagirID', 'Eng_Name', 'Heb_Name'], isIndividual: true, isGlobal: false)
                    ->sortable(['SagirID']),

                TextColumn::make('gender')
                    ->label(__('Gender')),

                TextColumn::make('approval_status')
                    ->label(__('Approval Status'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('is_dead')
                    ->label(__('Dead'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mother.full_name')
                    ->label(__('Mother'))
                    ->sortable(['Heb_Name'])
                    ->searchable(['SagirID', 'Eng_Name', 'Heb_Name'], isIndividual: true, isGlobal: false),

                TextColumn::make('breeding.birthing_date')
                    ->label(__('Breeding'))
                    ->date(),

                TextColumn::make('note')
                    ->label(__('Note'))
                    ->toggleable(),

                TextColumn::make('supervisor_notes')
                    ->label(__('Supervisor Notes'))
                    ->toggleable(),

                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('document')
                    ->label(__('Document'))
                    ->toggleable(),

                TextColumn::make('colorName.ColorNameHE')
                    ->label(__('Color'))
                    ->description(function (PrevBreedingRelatedDog $record): string {
                        return ($record->color_name?->id ?? "-") . " | " . ($record->color_name?->ColorNameEN ?? "~");
                    }, position: 'under')
                    ->sortable(['ColorNameHE'])
                    ->searchable(['ColorNameHE', 'ColorNameEN', 'OldCode'], isIndividual: true, isGlobal: false)
                    ->toggleable(),

                TextColumn::make('other_color')
                    ->label(__('Other Color'))
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(),

                TextColumn::make('hair')
                    ->label(__('Hair'))
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_submit')
                    ->label(__('Submitted'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.full_name')
                    ->label(__('Last Modified By'))
                    ->description(fn(PrevBreedingRelatedDog $record): string => $record->updated_by ?? '-')
                    ->sortable(['first_name', 'last_name'])
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'id'], isIndividual: true, isGlobal: false)
                    ->toggleable(),
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
            'index' => Pages\ListPrevBreedingRelatedDogs::route('/'),
            'create' => Pages\CreatePrevBreedingRelatedDog::route('/create'),
            'edit' => Pages\EditPrevBreedingRelatedDog::route('/{record}/edit'),
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
