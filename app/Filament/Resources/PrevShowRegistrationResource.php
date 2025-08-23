<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrevShowRegistrationResource\Pages;
use App\Models\PrevShowRegistration;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
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

class PrevShowRegistrationResource extends Resource
{
    protected static ?string $model = PrevShowRegistration::class;

    protected static ?string $slug = 'prev-show-registrations';

    protected static ?string $navigationIcon = 'fas-clipboard-check';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return __('Show Registration');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Show Registrations');
    }

    public static function getNavigationGroup(): string
    {
        return __('Shows Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Show Registrations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('ModificationDateTime'),

                DatePicker::make('CreationDateTime'),

                TextInput::make('DogId')
                    ->integer(),

                TextInput::make('DogName'),

                TextInput::make('BreedID')
                    ->integer(),

                TextInput::make('ColorID')
                    ->integer(),

                TextInput::make('HairID')
                    ->integer(),

                TextInput::make('SizeID')
                    ->integer(),

                DatePicker::make('BirthDate'),

                TextInput::make('GlobalSagirID'),

                TextInput::make('GenderID')
                    ->integer(),

                TextInput::make('Owner_Phone'),

                TextInput::make('Owner_City'),

                TextInput::make('Owner_Street'),

                TextInput::make('Owner_StNumber'),

                TextInput::make('Owner_Zip'),

                TextInput::make('Owner_Email'),

                TextInput::make('Owner_Mobile'),

                TextInput::make('Owner_IsMember')
                    ->integer(),

                TextInput::make('SpecialKey'),

                TextInput::make('SpecialClass')
                    ->integer(),

                TextInput::make('Owner_FirstName'),

                TextInput::make('Owner_LastName'),

                TextInput::make('Status')
                    ->integer(),

                TextInput::make('SagirID'),

                TextInput::make('Couples1_MDogName'),

                TextInput::make('Couples1_MSagirID'),

                TextInput::make('Couples2_FDogName'),

                TextInput::make('Couples2_FSagirID'),

                TextInput::make('bGidul1_DogName'),

                TextInput::make('bGidul2_DogName'),

                TextInput::make('bGidul3_DogName'),

                TextInput::make('bGidul4_DogName'),

                TextInput::make('bGidul5_DogName'),

                TextInput::make('bGidul1_SagirID'),

                TextInput::make('bGidul2_SagirID'),

                TextInput::make('bGidul3_SagirID'),

                TextInput::make('bGidul4_SagirID'),

                TextInput::make('bGidul5_SagirID'),

                TextInput::make('Gor1_DogName'),

                TextInput::make('Gor2_DogName'),

                TextInput::make('Gor3_DogName'),

                TextInput::make('Gor4_DogName'),

                TextInput::make('Gor5_DogName'),

                TextInput::make('Gor1_SagirID'),

                TextInput::make('Gor2_SagirID'),

                TextInput::make('Gor3_SagirID'),

                TextInput::make('Gor4_SagirID'),

                TextInput::make('Gor5_SagirID'),

                TextInput::make('Young_FullName'),

                TextInput::make('YoungBirthDate'),

                TextInput::make('Young_Address'),

                TextInput::make('Young_Phone'),

                TextInput::make('Young_BreedID'),

                TextInput::make('Notes'),

                TextInput::make('ShowID')
                    ->integer(),

                TextInput::make('IsbillingOK')
                    ->integer(),

                TextInput::make('IsPedigreeOK')
                    ->integer(),

                TextInput::make('IsManagerOK')
                    ->integer(),

                TextInput::make('InvoiceID')
                    ->integer(),

                TextInput::make('PrePayStatus'),

                MarkdownEditor::make('invoice_text'),

                TextInput::make('Gor_Parent_SagirID'),

                TextInput::make('bGidul6_SagirID'),

                TextInput::make('ClassID')
                    ->integer(),

                Select::make('registered_by')
                    ->relationship('registeredBy', 'name')
                    ->searchable(),

                TextInput::make('ShowID')
                    ->required()
                    ->integer(),

                TextInput::make('SagirID')
                    ->required()
                    ->integer(),

                TextInput::make('ClassID')
                    ->required()
                    ->integer(),

                Select::make('registered_by')
                    ->relationship('registeredBy', 'name')
                    ->searchable()
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?PrevShowRegistration $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?PrevShowRegistration $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ModificationDateTime')
                    ->date(),

                TextColumn::make('CreationDateTime')
                    ->date(),

                TextColumn::make('DogId'),

                TextColumn::make('DogName'),

                TextColumn::make('BreedID'),

                TextColumn::make('ColorID'),

                TextColumn::make('HairID'),

                TextColumn::make('SizeID'),

                TextColumn::make('BirthDate')
                    ->date(),

                TextColumn::make('GlobalSagirID'),

                TextColumn::make('GenderID'),

                TextColumn::make('Owner_Phone'),

                TextColumn::make('Owner_City'),

                TextColumn::make('Owner_Street'),

                TextColumn::make('Owner_StNumber'),

                TextColumn::make('Owner_Zip'),

                TextColumn::make('Owner_Email'),

                TextColumn::make('Owner_Mobile'),

                TextColumn::make('Owner_IsMember'),

                TextColumn::make('SpecialKey'),

                TextColumn::make('SpecialClass'),

                TextColumn::make('Owner_FirstName'),

                TextColumn::make('Owner_LastName'),

                TextColumn::make('Status'),

                TextColumn::make('SagirID'),

                TextColumn::make('Couples1_MDogName'),

                TextColumn::make('Couples1_MSagirID'),

                TextColumn::make('Couples2_FDogName'),

                TextColumn::make('Couples2_FSagirID'),

                TextColumn::make('bGidul1_DogName'),

                TextColumn::make('bGidul2_DogName'),

                TextColumn::make('bGidul3_DogName'),

                TextColumn::make('bGidul4_DogName'),

                TextColumn::make('bGidul5_DogName'),

                TextColumn::make('bGidul1_SagirID'),

                TextColumn::make('bGidul2_SagirID'),

                TextColumn::make('bGidul3_SagirID'),

                TextColumn::make('bGidul4_SagirID'),

                TextColumn::make('bGidul5_SagirID'),

                TextColumn::make('Gor1_DogName'),

                TextColumn::make('Gor2_DogName'),

                TextColumn::make('Gor3_DogName'),

                TextColumn::make('Gor4_DogName'),

                TextColumn::make('Gor5_DogName'),

                TextColumn::make('Gor1_SagirID'),

                TextColumn::make('Gor2_SagirID'),

                TextColumn::make('Gor3_SagirID'),

                TextColumn::make('Gor4_SagirID'),

                TextColumn::make('Gor5_SagirID'),

                TextColumn::make('Young_FullName'),

                TextColumn::make('YoungBirthDate'),

                TextColumn::make('Young_Address'),

                TextColumn::make('Young_Phone'),

                TextColumn::make('Young_BreedID'),

                TextColumn::make('Notes'),

                TextColumn::make('ShowID'),

                TextColumn::make('IsbillingOK'),

                TextColumn::make('IsPedigreeOK'),

                TextColumn::make('IsManagerOK'),

                TextColumn::make('InvoiceID'),

                TextColumn::make('PrePayStatus'),

                TextColumn::make('Gor_Parent_SagirID'),

                TextColumn::make('bGidul6_SagirID'),

                TextColumn::make('ClassID'),

                TextColumn::make('registeredBy.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ShowID'),

                TextColumn::make('SagirID'),

                TextColumn::make('ClassID'),

                TextColumn::make('registeredBy.name')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListPrevShowRegistrations::route('/'),
            'create' => Pages\CreatePrevShowRegistration::route('/create'),
            'edit' => Pages\EditPrevShowRegistration::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['registeredBy']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['registeredBy.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->registeredBy) {
            $details['RegisteredBy'] = $record->registeredBy->name;
        }

        return $details;
    }
}
