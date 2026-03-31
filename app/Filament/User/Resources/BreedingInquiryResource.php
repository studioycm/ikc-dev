<?php

namespace App\Filament\User\Resources;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\User\Resources\BreedingInquiryResource\Pages;
use App\Livewire\Legacy\Breeding\ClubMembershipCompact;
use App\Livewire\Legacy\Breeding\DogChecksTable;
use App\Models\BreedingInquiry;
use App\Models\PrevDog;
use App\Models\PrevUser;
use App\Services\Legacy\LegacyMembershipResolverService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Livewire as LivewireComponent;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BreedingInquiryResource extends Resource
{
    protected static ?string $model = BreedingInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Breeding Inquiry';

    public static function getNavigationLabel(): string
    {
        return __('New Litters');
    }

    public static function getModelLabel(): string
    {
        return __('New Litter');
    }

    public static function getPluralModelLabel(): string
    {
        return __('New Litters');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('inquiry')
                        ->label(__('Inquiry'))
                        ->columns(2)
                        ->schema([

                            TextInput::make('litter_report_name')
                                ->label(__('Litter Report Name'))
                                ->required()
                                ->columnSpan(1),

                            Group::make([
                                Section::make('female_section')
                                    ->heading(__('Dam Information'))
                                    ->schema([
                                        Select::make('female_sagir_id')
                                            ->label(__('Female'))
                                            ->hint(__('Search dogs by import number, sagir, chip or name'))
                                            ->required()
                                            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                            ->relationship(
                                                name: 'femaleDog',
                                                titleAttribute: 'SagirID',
                                                modifyQueryUsing: function (Builder $query): Builder {

                                                    $prevUserId = auth()->user()?->prev_user_id;

                                                    return $query
                                                        ->where('GenderID', LegacyDogGender::Female->value)
                                                        ->whereHas('owners', function (Builder $ownerQuery) use ($prevUserId) {
                                                            return $ownerQuery->where('users.id', $prevUserId ?? 0);
                                                        })
                                                        ->withCount('femaleBreedings');
                                                },
                                                ignoreRecord: true,
                                            )
                                            ->optionsLimit(20)
                                            ->searchDebounce(800)
                                            ->getOptionLabelFromRecordUsing(
                                                fn(PrevDog $record) => "{$record->SagirID} - {$record->full_name}"
                                            )
                                            ->live()
                                            ->afterStateHydrated(
                                                fn(Set $set, Get $get, ?string $state, Select $component) => self::hydrateFemale($get, $set, $component)
                                            )
                                            ->afterStateUpdated(
                                                fn(Set $set, Get $get, ?string $state, Select $component) => self::hydrateFemale($get, $set, $component)
                                            ),

                                        LivewireComponent::make(DogChecksTable::class, fn(Get $get): array => [
                                            'sagirId' => $get('female_sagir_id'),
                                            'role' => 'female',
                                            'title' => __('Dam validity and breeding checks'),
                                        ])
                                            ->hidden(fn(Get $get): bool => blank($get('female_sagir_id')))
                                            ->key(fn(Get $get): string => 'female-checks-' . ($get('female_sagir_id') ?: 'empty')),

                                    ])
                                    ->columnSpan(1),
                                Section::make('male_section')
                                    ->heading(__('Sire Information'))
                                    ->schema([
                                        Select::make('male_sagir_id')
                                            ->label(__('Male'))
                                            ->hint(__('Search dogs by import number, sagir, chip or name'))
                                            ->disabled(fn(Get $get) => blank($get('female_sagir_id')))
                                            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
                                            ->relationship(
                                                name: 'maleDog',
                                                titleAttribute: 'SagirID',
                                                modifyQueryUsing: function (Builder $query, Get $get): Builder {

                                                    $raceId = $get('female_race_id_state');

                                                    $query->where('GenderID', LegacyDogGender::Male->value)
                                                        ->withCount('maleBreedings');

                                                    if (blank($raceId)) {
                                                        return $query->whereRaw('1 = 0');
                                                    }

                                                    return $query->where('RaceID', $raceId);
                                                },
                                                ignoreRecord: true,
                                            )
                                            ->optionsLimit(20)
                                            ->searchDebounce(800)
                                            ->getOptionLabelFromRecordUsing(
                                                fn(PrevDog $record) => "{$record->SagirID} - {$record->full_name}"
                                            )
                                            ->live()
                                            ->afterStateHydrated(
                                                fn(Set $set, Get $get, ?string $state, Select $component) => self::hydrateMale($get, $set, $component)
                                            )
                                            ->afterStateUpdated(
                                                fn(Set $set, Get $get, ?string $state, Select $component) => self::hydrateMale($get, $set, $component)
                                            ),

                                        LivewireComponent::make(DogChecksTable::class, fn(Get $get): array => [
                                            'sagirId' => $get('male_sagir_id'),
                                            'role' => 'male',
                                            'title' => __('Sire validity and breeding checks'),
                                        ])
                                            ->hidden(fn(Get $get): bool => blank($get('male_sagir_id')))
                                            ->key(fn(Get $get): string => 'male-checks-' . ($get('male_sagir_id') ?: 'empty')),

                                    ])
                                    ->columnSpan(1),
                            ])->columns(2)
                                ->columnSpanFull(),

                            /*
                            |--------------------------------------------------------------------------
                            | CLUB MEMBERSHIP SECTION
                            |--------------------------------------------------------------------------
                            */

                            LivewireComponent::make(ClubMembershipCompact::class, fn(Get $get): array => [
                                'membershipState' => $get('club_membership_state'),
                            ])
                                ->hidden(fn(Get $get): bool => blank($get('female_sagir_id')))
                                ->key(fn(Get $get): string => 'club-membership-' . ($get('female_sagir_id') ?: 'empty'))
                                ->columnSpan(1),

                            ViewField::make('membership_badges')
                                ->view('legacy.breeding.fields.membership-badges')
                                ->viewData(fn(Get $get): array => [
                                    'sagirId' => $get('female_sagir_id'),
                                    'prevUserId' => auth()->user()?->prev_user_id,
                                    'strategies' => [
                                        'owner_breed_club',
                                        'owner_any_club',
                                        'at_least_one_co_owner_breed_club',
                                    ],
                                ])
                                ->visible(fn(Get $get) => filled($get('female_sagir_id')))
                                ->columnSpan(1),

                            Section::make(__('Breed Information'))
                                ->schema([
                                    Placeholder::make('breed_conditions')
                                        ->label(__('Breed Breeding Conditions'))
                                        ->columnSpanFull()
                                        ->content(fn() => __('Breed special breeding conditions will be displayed here.')),
                                ])
                                ->columns(2)
                                ->columnSpan(1),
                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 2: BREEDING
                    |--------------------------------------------------------------------------
                    */
                    Step::make('breeding')
                        ->label(__('Breeding'))
                        ->schema([
                            DatePicker::make('breeding_date')
                                ->label(__('Breeding Date'))
                                ->required(),
                            Section::make('breeder_rights_section')
                                ->heading(__('Breeder Rights'))
                                ->schema([
                                    Placeholder::make('breeder_rights_explanation')
                                        ->label(false)
                                        ->columnSpanFull()
                                        ->content(fn() => __('Breeder rights explanation will be displayed here.')),
                                    ToggleButtons::make('type')
                                        ->label(__('Transfer Breeder / Kennel'))
                                        ->options(function (Get $get) {
                                            $options = [
                                                'owner' => __('Without'),
                                                'female_co_owner' => __('Co-owner'),
                                                'male_owner' => __("Male's Owner"),
                                                'kennel' => __('Kennel'),
                                            ];
                                            // Hide 'archived' if 'other_field' is filled
                                            if (!filled($get('female_sagir_id'))) {
                                                unset($options['female_co_owner']);
                                            }

                                            // Hide 'published' if 'other_field' equals 'hide_pub'
                                            if (!filled($get('male_sagir_id'))) {
                                                unset($options['male_owner']);
                                            }

                                            return $options;
                                        })
                                        ->live()
                                        ->grouped()
                                        ->columnSpan(1)
                                        ->default('owner')
                                        ->dehydrated(false),
                                    // PrevUser Select (“Breeder”)
                                    Select::make('prev_breeder_id')
                                        ->label(__('Breeder'))
                                        ->required(fn($get) => $get('type') !== 'kennel')
                                        ->getSearchResultsUsing(fn($search, $get) => PrevUser::query()
                                            ->when($get('type') == 'owner', fn($q) => $q->where('id', auth()->user()->prev_user_id))
                                            ->when($get('type') == 'female_co_owner', fn($q) => $q->whereHas('dogs', fn($q2) => $q2->where('SagirID', $get('female_sagir_id')))
                                                ->where('id', '!=', auth()->user()->prev_user_id))
                                            ->when($get('type') == 'male_owner', fn($q) => $q->whereHas('dogs', fn($q2) => $q2->where('SagirID', $get('male_sagir_id'))))->limit(50)->get()->pluck('name', 'id')->toArray()
                                        )
                                        ->getOptionLabelUsing(fn($value): ?string => PrevUser::find($value)->name)
                                        ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone', 'email'])
                                        ->preload()
                                        ->columnSpan(1)
                                        ->visible(fn($get) => $get('type') !== 'kennel'),

                                    // PrevBreedingHouse Select (“Kennel”)
                                    Select::make('prev_breeding_house_id')
                                        ->label(__('Breeding House'))
                                        ->relationship('breedingHouse', 'HebName')
                                        ->searchable(['HebName', 'EngName'])
                                        ->columnSpan(1)
                                        ->visible(fn($get) => $get('type') === 'kennel'),

                                    // SMS Request Approval Action
                                    //                                            Action::make('sms')
                                    //                                                ->label(__('SMS Request Approval'))
                                    //                                                ->icon('heroicon-o-chat')
                                    //                                                ->size('sm')
                                    //                                                ->button(),

                                ]),
                        ])
                        ->columns(2),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 3: LITTER
                    |--------------------------------------------------------------------------
                    */
                    Step::make('litter')
                        ->label(__('Litter'))
                        ->schema([

                            DatePicker::make('birthing_date')
                                ->label(__('Whelping Date')),

                            Repeater::make('puppies')
                                ->label(__('Puppies'))
                                ->default([])
                                ->schema([
                                    Hidden::make('uuid')
                                        ->default(fn() => (string)Str::uuid())
                                        ->dehydrated(true),

                                    TextInput::make('name')
                                        ->label(__('Name')),

                                    ToggleButtons::make('gender')
                                        ->options([
                                            'male' => __('Male'),
                                            'female' => __('Female'),
                                        ])
                                        ->grouped(),

                                    TextInput::make('chip')
                                        ->label(__('Chip')),

                                    ToggleButtons::make('vaccinated')
                                        ->options([
                                            'yes' => __('Yes'),
                                            'no' => __('No'),
                                        ])
                                        ->grouped(),

                                    DatePicker::make('vaccinated_date')
                                        ->nullable()
                                        ->visible(fn(Get $get) => $get('vaccinated') === 'yes'),

                                    ToggleButtons::make('alive')
                                        ->options([
                                            'yes' => __('Yes'),
                                            'no' => __('No'),
                                        ])
                                        ->default('yes')
                                        ->grouped(),
                                ])
                                ->columns(3),
                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | STEP 4: INSPECTION
                    |--------------------------------------------------------------------------
                    */
                    Step::make('inspection')
                        ->label(__('Inspection'))
                        ->schema([
                            Select::make('review_type')
                                ->options([
                                    'breeding_promoter' => __('Breed promoter'),
                                    'breeding_group' => __('Breeding group'),
                                    'not_matter' => __('Does not matter'),
                                    'office_choice' => __('Office choice'),
                                ])
                                ->nullable(),

                            ToggleButtons::make('payment_type')
                                ->options([
                                    'phone_payment' => __('Phone Payment'),
                                    'credit_card' => __('Credit Card'),
                                    'cash' => __('Cash'),
                                ])
                                ->nullable(),
                        ])
                        ->columns(2),

                ])
                    ->persistStepInQueryString('step')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'breeding-wizard']),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['femaleDog', 'maleDog'])->orderBy('created_at', 'asc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('litter_report_name')
                    ->label(__('Litter Report Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('femaleDog.SagirID')
                    ->label(__('Dam'))
                    ->description(fn(BreedingInquiry $record) => $record->femaleDog->full_name),
                Tables\Columns\TextColumn::make('maleDog.SagirID')
                    ->label(__('Sire'))
                    ->description(fn(BreedingInquiry $record) => $record->maleDog->full_name),
                Tables\Columns\TextColumn::make('breeding_date')
                    ->label(__('Breeding Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthing_date')
                    ->label(__('Birthing Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label(__('Submitted At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modal(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBreedingInquiries::route('/'),
            'create' => Pages\CreateBreedingInquiry::route('/create'),
            'view' => Pages\ViewBreedingInquiry::route('/{record}'),
            'edit' => Pages\EditBreedingInquiry::route('/{record}/edit'),
        ];
    }

    protected static function hydrateFemale(
        Get $get,
        Set $set,
        ?Select $component
    ): void
    {

        $femaleId = $get('female_sagir_id');

        if (blank($femaleId)) {
            self::resetFemale($set);

            return;
        }

        $dog = $component?->getSelectedRecord();

        if (!$dog instanceof PrevDog) {
            $dog = PrevDog::query()
                ->with([
                    'breed',
                    'titles',
                ])
                ->withCount('femaleBreedings')
                ->where('SagirID', $femaleId)
                ->first();
        } else {
            $dog->loadMissing(['breed', 'titles'])
                ->loadCount('femaleBreedings');
        }

        if (!$dog) {
            self::resetFemale($set);

            return;
        }

        // Core info
        $set('female_age_state', $dog->age_years);
        $set('female_breedings_count_state', $dog->female_breedings_count);
        $set('female_dna_state', $dog->DnaID);
        $set('female_red_pedigree_state', $dog->RedPedigree ?? false);
        $set('female_race_id_state', $dog->RaceID);

        // Suitability logic
        $set('female_suitable_state', self::calculateSuitability($dog));

        // Club
        $resolver = app(LegacyMembershipResolverService::class);

        $membershipSummary = $resolver->resolveSummaryForDogAndUser($dog);

        $set('club_membership_state', $membershipSummary);
    }

    protected static function calculateSuitability(PrevDog $dog): array
    {
        return [
            'has_dna' => filled($dog->DnaID),
            'is_adult' => $dog->age_years >= 1,
            'red_pedigree' => (bool)$dog->RedPedigree,
            'breeding_limit_ok' => $dog->female_breedings_count < 6,
        ];
    }

    protected static function hydrateMale(
        Get $get,
        Set $set,
        ?Select $component
    ): void
    {

        $maleId = $get('male_sagir_id');

        if (blank($maleId)) {
            self::resetMale($set);

            return;
        }

        $dog = $component?->getSelectedRecord();

        if (!$dog instanceof PrevDog) {
            $dog = PrevDog::query()
                ->withCount('maleBreedings')
                ->where('SagirID', $maleId)
                ->first();
        } else {
            $dog->loadCount('maleBreedings');
        }

        if (!$dog) {
            self::resetMale($set);

            return;
        }

        $set('male_age_state', $dog->age_years);
        $set('male_breedings_count_state', $dog->male_breedings_count);
        $set('male_dna_state', $dog->DnaID);
        $set('male_red_pedigree_state', $dog->RedPedigree ?? false);
    }

    protected static function resetFemale(Set $set): void
    {
        $set('female_age_state', null);
        $set('female_breedings_count_state', null);
        $set('female_dna_state', null);
        $set('female_red_pedigree_state', null);
        $set('female_race_id_state', null);
        $set('female_suitable_state', null);
        $set('club_membership_state', null);
    }

    protected static function resetMale(Set $set): void
    {
        $set('male_age_state', null);
        $set('male_breedings_count_state', null);
        $set('male_dna_state', null);
        $set('male_red_pedigree_state', null);
    }
}
