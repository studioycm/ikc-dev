<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowDogResource\Pages;
use App\Models\PrevShowDog;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevShowDogResource extends Resource
{
    protected static ?string $model = PrevShowDog::class;

    protected static ?string $slug = 'show-dogs';

    protected static ?string $navigationIcon = 'fas-dog';

    protected static ?int $navigationSort = 64;

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

                Select::make('OwnerID')
                    ->relationship('ownerID', 'name')
                    ->searchable(),

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

                TextInput::make('SagirID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                TextInput::make('ArenaID')
                    ->required()
                    ->integer(),

                TextInput::make('ShowRegistrationID')
                    ->required()
                    ->integer(),

                TextInput::make('ClassID')
                    ->required()
                    ->integer(),

                Select::make('OwnerID')
                    ->relationship('ownerID', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('new_show_registration_id')
                    ->required()
                    ->integer(),

                TextInput::make('BreedID')
                    ->required()
                    ->integer(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?PrevShowDog $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?PrevShowDog $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('DataID'),

                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('ShowID'),

                TextColumn::make('SagirID'),

                TextColumn::make('GlobalSagirID'),

                TextColumn::make('OrderID'),

                TextColumn::make('ownerID.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('BirthDate')
                    ->date(),

                TextColumn::make('BreedID'),

                TextColumn::make('SizeID'),

                TextColumn::make('GenderID'),

                TextColumn::make('DogName'),

                TextColumn::make('ShowRegistrationID'),

                TextColumn::make('ClassID'),

                TextColumn::make('OwnerName'),

                TextColumn::make('OwnerMobile'),

                TextColumn::make('BeitGidulName'),

                TextColumn::make('HairID'),

                TextColumn::make('ColorID'),

                TextColumn::make('MainArenaID'),

                TextColumn::make('ArenaID'),

                TextColumn::make('ShowBreedID'),

                TextColumn::make('MobileNumber'),

                TextColumn::make('OwnerEmail'),

                TextColumn::make('new_show_registration_id'),

                TextColumn::make('present')
                    ->date(),

                TextColumn::make('SagirID'),

                TextColumn::make('ShowID'),

                TextColumn::make('ArenaID'),

                TextColumn::make('ShowRegistrationID'),

                TextColumn::make('ClassID'),

                TextColumn::make('ownerID.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('new_show_registration_id'),

                TextColumn::make('BreedID'),
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
            'index' => Pages\ListPrevShowDogs::route('/'),
            'create' => Pages\CreatePrevShowDog::route('/create'),
            'edit' => Pages\EditPrevShowDog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    //    public static function getGlobalSearchEloquentQuery(): Builder
    //    {
    //        return parent::getGlobalSearchEloquentQuery()->with(['ownerID']);
    //    }
    //
    //    public static function getGloballySearchableAttributes(): array
    //    {
    //        return ['ownerID.name'];
    //    }
    //
    //    public static function getGlobalSearchResultDetails(Model $record): array
    //    {
    //        $details = [];
    //
    //        if ($record->ownerID) {
    //            $details['OwnerID'] = $record->ownerID->name;
    //        }
    //
    //        return $details;
    //    }
}
