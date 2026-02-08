<?php

namespace App\Filament\Resources;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\Resources\PrevBreedingResource\Pages;
use App\Models\PrevBreeding;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrevBreedingResource extends Resource
{
    protected static ?string $model = PrevBreeding::class;

    protected static ?string $slug = 'prev-breedings';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('Litter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breedings');
    }

    public static function getNavigationGroup(): string
    {
        return __('Breedings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Breedings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('inquiry')
                        ->label(__('Inquiry'))
                        ->description(__('Preliminary inquiry information'))
                        ->extraAttributes([
                            'class' => 'breeding-wizard-step-inquiry',
                        ])
                        ->schema([
                            Group::make([
                                    Select::make('SagirId')
                                        ->label(__('Female'))
                                        ->hint(__('Search dogs by import number, sagir, chip or name'))
                                        ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                        ->relationship('female', 'SagirID', modifyQueryUsing: fn(Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Female->value), ignoreRecord: true)
                                        ->optionsLimit(20)
                                        ->default('504740')
                                        ->searchDebounce(1500)
                                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->SagirID} - {$record->full_name}")
                                        ->columnSpan(2),

                                    Toggle::make('Female_DNA')
                                        ->label(__('Female DNA'))
                                        ->inline(false)
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger'),

                                    ToggleButtons::make('less_than_8_years')
                                        ->label(__('Less than 8 years'))
                                        ->options([
                                            'yes' => __('Yes'),
                                            'no' => __('No'),
                                        ])
                                        ->grouped(),

                                    ToggleButtons::make('more_than_18_months')
                                        ->label(__('More than 18 months'))
                                        ->options([
                                            'yes' => __('Yes'),
                                            'no' => __('No'),
                                        ])
                                        ->grouped(),
                            ])
                                ->columns(5),
                            Group::make([
                                    Select::make('MaleSagirId')
                                        ->label(__('Male'))
                                        ->hint(__('Search dogs by import number, sagir, chip or name'))
                                        ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                        ->relationship('male', 'SagirID', modifyQueryUsing: fn(Builder $query) => $query->where('GenderID', '=', LegacyDogGender::Male->value), ignoreRecord: true)
                                        ->optionsLimit(20)
                                        ->searchDebounce(1500)
                                        ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->SagirID} - {$record->full_name}")
                                        ->columnSpan(2),

                                    Toggle::make('Male_DNA')
                                        ->label(__('Male DNA'))
                                        ->inline(false)
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger'),

                                    Toggle::make('Foreign_Male_Records')
                                        ->label(__('Foreign Male Records'))
                                        ->inline(false)
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger'),
                            ])
                                ->columns(5),
                        ])
                        ->columns(2),

                    Step::make('breeding')
                        ->label(__('Breeding'))
                        ->description(__('Breeding information'))
                        ->extraAttributes([
                            'class' => 'breeding-wizard-step-breeding',
                        ])
                        ->schema([
                            // --- MATING DETAILS ---
                            DatePicker::make('BreddingDate')
                                ->label(__('Breeding Date')),

                            Select::make('breeding_house_id')
                                ->label(__('Beit Gidul'))
                                ->relationship('breedinghouse', 'HebName')
                                ->searchable(['HebName', 'EngName', 'GidulCode']),

                            Select::make('created_by')
                                ->label(__('Breeder'))
                                ->relationship('createdBy', 'first_name')
                                ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en']),


                        ])
                        ->columns(4),

                    Step::make('litter')
                        ->label(__('Litter'))
                        ->description(__('Litter information'))
                        ->extraAttributes([
                            'class' => 'breeding-wizard-step-litter',
                        ])
                        ->schema([
                            Section::make('general')
                                ->heading(__('General'))
                                ->schema([
                                    // --- DATES & SETTINGS ---
                                    DatePicker::make('birthing_date')
                                        ->label(__('Birthing date'))
                                        ->columnSpan(2),

                                    // --- PUPPY COUNTS ---
                                    TextInput::make('live_male_puppie')
                                        ->label(__('Live male puppie'))
                                        ->integer(),

                                    TextInput::make('live_female_puppie')
                                        ->label(__('Live female puppie'))
                                        ->integer(),

                                    TextInput::make('dead_male_puppie')
                                        ->label(__('Dead male puppie'))
                                        ->integer(),

                                    TextInput::make('dead_female_puppie')
                                        ->label(__('Dead female puppie'))
                                        ->integer(),

                                    TextInput::make('total_dead')
                                        ->label(__('Total dead'))
                                        ->integer(),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                            Section::make('puppies_list')
                                ->heading(__('Puppies list'))
                                ->schema([
                                    Repeater::make('puppies')
                                        ->label(false)
                                        ->addActionLabel(__('Add puppy'))
                                        ->addActionAlignment(Alignment::Start)
                                        ->schema([
                                            TextInput::make('name')
                                                ->label(__('Name')),
                                            ToggleButtons::make('gender')
                                                ->label(__('Gender'))
                                                ->options([
                                                    'male' => __('Male'),
                                                    'female' => __('Female'),
                                                ])
                                                ->grouped(),
                                            TextInput::make('chip')
                                                ->label(__('Chip')),
                                            ToggleButtons::make('vaccinated')
                                                ->label(__('Vaccinated'))
                                                ->options([
                                                    'yes' => __('Yes'),
                                                    'no' => __('No'),
                                                ])
                                                ->grouped(),
                                            DatePicker::make('vaccinated_date')
                                                ->label(__('Vaccination date'))
                                                ->nullable(),
                                            ToggleButtons::make('alive')
                                                ->label(__('Alive'))
                                                ->options([
                                                    'yes' => __('Yes'),
                                                    'no' => __('No'),
                                                ])
                                                ->default('yes')
                                                ->grouped(),
                                        ])
                                        ->columns(6)
                                ])
                                ->columnSpan(3),
                        ])
                        ->columns(4),

                    Step::make('inspection')
                        ->label(__('Inspection'))
                        ->description(__('Scheduling a litter inspection'))
                        ->extraAttributes([
                            'class' => 'breeding-wizard-step-inspection',
                        ])
                        ->schema(components: [
                            Select::make('review_type')
                                ->label(__('Review type'))
                                ->options([
                                    'breeding_promoter' => __('Breed promoter'),
                                    'breeding_group' => __('Breeding group'),
                                    'not_matter' => __('Does not matter'),
                                    'office_choice' => __('Office choice'),
                                ])
                                ->nullable(),

                            // --- FINANCIALS ---
                            ToggleButtons::make('payment_type')
                                ->label(__('Payment Type'))
                                ->options([
                                    'phone_payment' => __('Phone Payment'),
                                    'credit_card' => __('Credit Card'),
                                    'cash' => __('Cash'),
                                ])
                                ->grouped()
                                ->nullable(),

                        ])
                        ->columns(4),
                ])
                    ->persistStepInQueryString('step')
                    ->extraAttributes(['class' => 'breeding-wizard'])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Relationships (Using 'female' and 'male' relations from Breeding model)
                TextColumn::make('female.Eng_Name')
                    ->label('Mother (Dam)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('male.Eng_Name')
                    ->label('Father (Sire)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('BreddingDate')
                    ->label('Breeding Date')
                    ->date()
                    ->sortable(),

                // Booleans - Rules & Checks
                IconColumn::make('Rules_IsOwner')
                    ->label('Is Owner')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('BreedMismatch')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_More_Than_5')
                    ->label('Male > 5 Litters')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_More_Than_2')
                    ->label('Male > 2 Litters')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Male_DNA')
                    ->label('Male DNA')
                    ->boolean(),

                IconColumn::make('Female_DNA')
                    ->label('Female DNA')
                    ->boolean(),

                IconColumn::make('Male_Breeding_Not_Approved')
                    ->label('Sire Not Approved')
                    ->boolean()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Female_Breeding_Not_Approved')
                    ->label('Dam Not Approved')
                    ->boolean()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('Foreign_Male_Records')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Statistics
                TextColumn::make('female_rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('male_rebreed')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('generations_note')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Puppies Count
                TextColumn::make('live_male_puppie')
                    ->label('Live M')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('live_female_puppie')
                    ->label('Live F')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('dead_male_puppie')
                    ->label('Dead M')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('dead_female_puppie')
                    ->label('Dead F')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_dead')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status & Dates
                TextColumn::make('review_type')
                    ->badge()
                    ->color('info'),

                TextColumn::make('birthing_date')
                    ->label('Whelping Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('filled_step')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved', 'completed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Financials (Assuming ILS based on 'Asia/Jerusalem' timezone in context)
                TextColumn::make('payment_type')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payment_status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price_per_dog')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_payment')
                    ->money('ILS')
                    ->sortable(),

                TextColumn::make('total_refund')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Age Validation
                IconColumn::make('less_than_8_years')
                    ->label('< 8 Years')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('more_than_18_months')
                    ->label('> 18 Months')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Data Privacy
                IconColumn::make('publish_data')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('share_data')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('responsiable_owner')
                    ->label('Responsible Owner')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('createdBy.name')
                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en'])
                    ->sortable(['last_name', 'first_name']),

                TextColumn::make('breedinghouse.name')
                    ->searchable(['HebName', 'EngName', 'GidulCode'])
                    ->sortable(['HebName']),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevBreedings::route('/'),
            'create' => Pages\CreatePrevBreeding::route('/create'),
            'edit' => Pages\EditPrevBreeding::route('/{record}/edit'),
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
