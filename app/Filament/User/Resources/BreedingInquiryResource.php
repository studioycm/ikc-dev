<?php

namespace App\Filament\User\Resources;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\User\Resources\BreedingInquiryResource\Pages;
use App\Models\BreedingInquiry;
use App\Models\PrevDog;
use App\Services\Legacy\PrevClubMembershipResolverService;
use Filament\Forms\Components\{DatePicker,
    Group,
    Hidden,
    Placeholder,
    Repeater,
    Section,
    Select,
    TextInput,
    ToggleButtons};
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

    public static function getModelLabel(): string
    {
        return __('Breeding Inquiry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Breeding Inquiries');
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
                        ->schema([

                            TextInput::make('litter_report_name')
                                ->label(__('Litter Report Name'))
                                ->required(),

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
                                        Placeholder::make('suitability')
                                            ->label(__('DNA Test'))
                                            ->content(fn(Get $get) => $get('female_suitable_state.has_dna')),

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
                                    ])
                                    ->columnSpan(1),
                            ])->columns(2),

                            /*
                            |--------------------------------------------------------------------------
                            | CLUB MEMBERSHIP SECTION
                            |--------------------------------------------------------------------------
                            */

                            Section::make(__('Club Information'))
                                ->schema([
                                    Placeholder::make('club_name')
                                        ->label(__('Club'))
                                        ->content(fn(Get $get) => $get('club_membership_state.club_name') ?? '---'
                                        ),
                                    Placeholder::make('membership_status')
                                        ->label(__('Membership Status'))
                                        ->content(fn(Get $get) => $get('club_membership_state.status_label') ?? '---'
                                        ),
                                    Placeholder::make('membership_expiration_date')
                                        ->label(__('Valid Until'))
                                        ->content(fn(Get $get) => $get('club_membership_state.membership')->expire_date
                                            ? $get('club_membership_state.membership')->expire_date->format('d/m/Y')
                                            : '---'
                                        ),
                                    Placeholder::make('club_prices')
                                        ->label(__('Club Price'))
                                        ->content(function (Get $get) {
                                            $status_key = $get('club_membership_state.status_key');
                                            $prices = $get('club_membership_state.prices');
                                            if (!$prices) {
                                                return '---';
                                            }
                                            return $status_key === 'active'
                                                ? $prices->get('member') . 'ש"ח לאחר הנחה'
                                                : $prices->get('non_member') . 'ש"ח';
                                        }),

                                    Placeholder::make('club_conditions')
                                        ->label(__('Club Breeding Conditions'))
                                        ->columnSpanFull()
                                        ->content(fn() => __('Club special breeding conditions will be displayed here.')),
                                ])
                                ->columns(4)
                                ->visible(fn(Get $get) => filled($get('female_sagir_id'))),
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
                                        ->nullable(),

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
                    ->columnSpan('2xl')
                    ->extraAttributes(['class' => 'breeding-wizard']),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('litter_report_name')
                    ->label(__('Litter Report Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('female_sagir_id')
                    ->label(__('Female'))
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('male_sagir_id')
                    ->label(__('Male'))
                    ->searchable()
                    ->numeric()
                    ->sortable(),
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
        Get     $get,
        Set     $set,
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
        $resolver = app(PrevClubMembershipResolverService::class);

        $membership = $resolver->resolveForFemaleDog($dog);

        $set('club_membership_state', $membership);
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
        Get     $get,
        Set     $set,
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
