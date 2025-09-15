<?php

namespace App\Filament\Resources;

use App\Enums\Legacy\LegacyDogGender;
use App\Enums\Legacy\LegacyDogSize;
use App\Enums\Legacy\LegacyDogStatus;
use App\Enums\Legacy\LegacyPedigreeColor;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Filament\Resources\PrevDogResource\Pages;
use App\Models\PrevBreed;
use App\Models\PrevColor;
use App\Models\PrevDog;
use App\Models\PrevHair;
use App\Models\PrevUser;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs as FormTabs;
use Filament\Forms\Components\Tabs\Tab as FormTab;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

// use Filament\Tables\Filters\QueryBuilder;
// use Filament\Tables\Filters\SelectFilter;
// use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
// use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
// use Filament\Support\Facades\FilamentColor;
// use App\Filament\Resources\PrevDogResource\RelationManagers;
// infolist class
// use Filament\Infolists\Components\KeyValueEntry;

class PrevDogResource extends Resource
{
    protected static ?string $model = PrevDog::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'fas-paw';

//    protected static ?string $recordRouteKeyName = 'SagirID';

    //    public static function getNavigationBadge(): ?string
    //    {
    //        return (string) static::$model::count();
    //    }

    public static function getModelLabel(): string
    {
        return __('dog/model/general.labels.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dog/model/general.labels.plural');
    }

    public static function getNavigationGroup(): string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('dog/model/general.labels.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                FormTabs::make('prevDogFormTabs')
                    ->tabs([
                        FormTab::make('general')
                            ->schema([
                                Section::make('identity')
                                    ->schema([
                                        Forms\Components\Hidden::make('sagir_prefix'),
                                        Forms\Components\TextInput::make('SagirID')
                                            ->label(__('Sagir'))
                                            ->numeric()
                                            ->extraAttributes(fn(Model $record): array => ['class' => 'dark:disabled:text-white fi-form-sagir fi-form-sagir-' . $record->sagir_prefix?->getColor()])
                                            ->prefix(fn(Model $record): HtmlString => $record->sagir_prefix?->code() ? new HtmlString('<span class="fi-form-sagir">' . $record->sagir_prefix?->code() . '</span>') : new HtmlString('<span>---</span>'))
                                            ->suffixAction(
                                                Action::make('setPrefix')
                                                    ->label(__('Prefix'))
                                                    ->icon('fas-chevron-circle-down')
                                                    ->color(fn(Model $record): string => 'white')
                                                    ->modalHeading(__('Select SAGIR Prefix'))
                                                    ->form([
                                                        Forms\Components\Select::make('prefix')
                                                            ->label(__('Sagir Prefix'))
                                                            ->options(LegacySagirPrefix::class)
                                                            ->required(),
                                                    ])
                                                    ->action(function (array $data, Forms\Get $get, Forms\Set $set): void {
                                                        $set('sagir_prefix', (int)$data['prefix']);
                                                    })
                                            )
                                            ->disabled(),
                                        Forms\Components\TextInput::make('Heb_Name')
                                            ->label(__('Hebrew Name'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('Eng_Name')
                                            ->label(__('English Name'))
                                            ->maxLength(200),
                                        Forms\Components\ToggleButtons::make('GenderID')
                                            ->label(__('Gender'))
                                            ->grouped()
                                            ->options(LegacyDogGender::class),
                                        Forms\Components\DatePicker::make('BirthDate')
                                            ->label(__('Birth Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('yyyy-mm-dd')
                                            ->displayFormat('d-m-Y')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Forms\Components\DatePicker::make('RegDate')
                                            ->label(__('Registration Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('yyyy-mm-dd')
                                            ->displayFormat('d-m-Y')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Forms\Components\TextInput::make('Chip')
                                            ->label(__('Chip'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('DnaID')
                                            ->label(__('DNA ID'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('ImportNumber')
                                            ->label(__('Import Number'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('Chip_2')
                                            ->label(__('Chip 2'))
                                            ->maxLength(255),
                                        Forms\Components\ToggleButtons::make('Status')
                                            ->label(__('Status'))
                                            ->options(LegacyDogStatus::class)
                                            ->grouped()
                                            ->nullable()
                                            ->columnSpan(2),
                                    ])
                                    ->heading(__('Identity'))
                                    ->columns(4),
                                Section::make('breed_appearance')
                                    ->schema([
                                        Select::make('RaceID')
                                            ->label(__('Breed'))
                                            ->relationship('breed', 'BreedName')
                                            ->searchable(),
                                        Select::make('ColorID')
                                            ->label(__('Color'))
                                            ->relationship('color', 'ColorNameHE')
                                            ->searchable(),
                                        Select::make('HairID')
                                            ->label(__('Hair'))
                                            ->relationship('hair', 'HairNameHE')
                                            ->searchable(),
                                        Forms\Components\Select::make('GroupID')
                                            ->label(__('Group ID'))
                                            ->options(array_combine(range(0, 7), range(0, 7)))
                                            ->searchable(),
                                        Forms\Components\ToggleButtons::make('SizeID')
                                            ->label(__('Size'))
                                            ->options(LegacyDogSize::class)
                                            ->grouped(),
                                    ])
                                    ->heading(__('Breed & Appearance'))
                                    ->columns(4),
                                Section::make('miscellaneous')
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('ModificationDateTime')
                                            ->label(__('Modified On'))
                                            ->format('Y-m-d H:i:s')
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->displayFormat('d-m-Y H:i:s')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection()
                                            ->default(now()),
                                        Forms\Components\Toggle::make('encoding')
                                            ->label(__('Encoding Issue'))
                                            ->inline(false),
                                        Forms\Components\Toggle::make('is_correct')
                                            ->label(__('Is Correct'))
                                            ->inline(false),
                                        Forms\Components\Toggle::make('not_relevant')
                                            ->label(__('Not Relevant'))
                                            ->inline(false),
                                        Forms\Components\Select::make('RemarkCode')
                                            ->label(__('Remark Code'))
                                            ->options(fn() => array_combine(range(0, 36), range(0, 36)))
                                            ->searchable(),
                                        Forms\Components\Textarea::make('Notes')
                                            ->label(__('Notes'))
                                            ->maxLength(1000),
                                        Forms\Components\Textarea::make('Notes_2')
                                            ->label(__('Notes (2)'))
                                            ->maxLength(1000),
                                        Forms\Components\Textarea::make('message')
                                            ->label(__('Message'))
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('message_test')
                                            ->label(__('Message Test'))
                                            ->maxLength(255),
                                    ])
                                    ->heading(__('Miscellaneous'))
                                    ->columns(4),
                            ])
                            ->label(__('General')),

                        FormTab::make('owners')
                            ->schema([
                                Section::make('ownership')
                                    ->schema([
                                        Select::make('CurrentOwnerId')
                                            ->label(__('Owner Pre 2022'))
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return PrevUser::query()
                                                    ->where(function ($q) use ($search) {
                                                        $q->where('first_name', 'like', "%{$search}%")
                                                            ->orWhere('last_name', 'like', "%{$search}%")
                                                            ->orWhere('first_name_en', 'like', "%{$search}%")
                                                            ->orWhere('last_name_en', 'like', "%{$search}%")
                                                            ->orWhere('owner_code', 'like', "%{$search}%");
                                                    })
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn($u) => [$u->owner_code => $u->name])
                                                    ->all();
                                            })
                                            ->getOptionLabelUsing(fn($value): ?string => PrevUser::query()->where('owner_code', $value)->first()?->name),
                                        Forms\Components\DatePicker::make('OwnershipDate')
                                            ->label(__('Ownership Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('yyyy-mm-dd')
                                            ->displayFormat('d-m-Y')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Forms\Components\TextInput::make('BeitGidulID')
                                            ->label(__('Beit Gidul ID'))
                                            ->numeric(),
                                        Forms\Components\TextInput::make('BeitGidulName')
                                            ->label(__('Beit Gidul Name'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('GidulShowType')
                                            ->label(__('Gidul Show Type'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('GrowerId')
                                            ->label(__('Breeder ID'))
                                            ->numeric(),
                                        Forms\Components\TextInput::make('Breeder_Name')
                                            ->label(__('Breeder Name'))
                                            ->maxLength(300),
                                        Forms\Components\TextInput::make('Foreign_Breeder_name')
                                            ->label(__('Foreign Breeder'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('Breeding_ManagerID')
                                            ->label(__('Breeding Manager ID'))
                                            ->numeric(),
                                    ])
                                    ->heading(__('Pre 2022 Ownership'))
                                    ->columns(4),
                            ])
                            ->label(__('Owners Pre 2022')),

                        FormTab::make('pedigree_ancestry')
                            ->schema([
                                Section::make('pedigree')
                                    ->schema([
                                        Select::make('FatherSAGIR')
                                            ->label(__('Father'))
                                            ->columnSpan(2)
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return PrevDog::query()
                                                    ->where('SagirID', 'like', "%{$search}%")
                                                    ->orWhere('Heb_Name', 'like', "%{$search}%")
                                                    ->orWhere('Eng_Name', 'like', "%{$search}%")
                                                    ->orWhere('Chip', 'like', "%{$search}%")
                                                    ->orWhere('ImportNumber', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn($d) => [$d->SagirID => $d->SagirID . ' - ' . $d->full_name])
                                                    ->all();
                                            })
                                            ->getOptionLabelUsing(function ($value) {
                                                $d = PrevDog::query()->where('SagirID', $value)->first();

                                                return $d ? ($d->SagirID . ' - ' . $d->full_name) : null;
                                            })
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('Eng_Name')
                                                    ->label(__('English Name'))
                                                    ->maxLength(200),
                                                Forms\Components\TextInput::make('Heb_Name')
                                                    ->label(__('Hebrew Name'))
                                                    ->maxLength(200),
                                                Forms\Components\TextInput::make('ImportNumber')
                                                    ->label(__('Import Number'))
                                                    ->maxLength(200),
                                                Forms\Components\DatePicker::make('BirthDate')
                                                    ->label(__('Birth Date'))
                                                    ->timezone('Asia/Jerusalem')
                                                    ->native(false)
                                                    ->locale('he')
                                                    ->format('yyyy-mm-dd')
                                                    ->displayFormat('d-m-Y')
                                                    ->weekStartsOnSunday()
                                                    ->closeOnDateSelection(),
                                                Select::make('RaceID')
                                                    ->label(__('Breed'))
                                                    ->relationship('breed', 'BreedName')
                                                    ->searchable(),
                                                Select::make('HairID')
                                                    ->label(__('Hair'))
                                                    ->relationship('hair', 'HairNameHE')
                                                    ->searchable(),
                                                Select::make('ColorID')
                                                    ->label(__('Color'))
                                                    ->relationship('color', 'ColorNameHE')
                                                    ->searchable(),
                                                Forms\Components\ToggleButtons::make('GenderID')
                                                    ->label(__('Gender'))
                                                    ->grouped()
                                                    ->options(LegacyDogGender::class)
                                                    ->default(fn(Forms\Get $get) => LegacyDogGender::Male->value),
                                                Forms\Components\Select::make('sagir_prefix')
                                                    ->label(__('Sagir Prefix'))
                                                    ->options(\App\Enums\Legacy\LegacySagirPrefix::class)
                                                    ->default(\App\Enums\Legacy\LegacySagirPrefix::NUL->value),
                                                Forms\Components\TextInput::make('Chip')
                                                    ->label(__('Chip'))
                                                    ->maxLength(200),
                                                Forms\Components\TextInput::make('DnaID')
                                                    ->label(__('DNA ID'))
                                                    ->maxLength(200),
                                                Forms\Components\TextInput::make('breeder_name')
                                                    ->label(__('Breeder Name'))
                                                    ->maxLength(300),
                                                Forms\Components\Textarea::make('HealthNotes')
                                                    ->label(__('Health Notes'))
                                                    ->maxLength(4000),
                                                Forms\Components\TextInput::make('PedigreeNotes')
                                                    ->label(__('Titles'))
                                                    ->helperText('Comma separated'),
                                                Forms\Components\Textarea::make('Notes')
                                                    ->label(__('Notes'))
                                                    ->maxLength(1000),
                                                Forms\Components\TextInput::make('SagirID')
                                                    ->label(__('Sagir'))
                                                    ->numeric()
                                                    ->helperText(__('Temporary value will be auto-set')),
                                                Forms\Components\TextInput::make('DataID')
                                                    ->label(__('Data ID'))
                                                    ->numeric()
                                                    ->helperText(__('Temporary value will be auto-set')),
                                            ])
                                            ->createOptionUsing(function (array $data, Forms\Get $get): int|string {
                                                // Auto gender: father
                                                $data['GenderID'] = \App\Enums\Legacy\LegacyDogGender::Male->value;

                                                // Default sagir_prefix to NUL if empty
                                                $data['sagir_prefix'] = $data['sagir_prefix'] ?? \App\Enums\Legacy\LegacySagirPrefix::NUL->value;

                                                // Auto-select breed from current "son" record RaceID if not chosen
                                                /** @var PrevDog|null $current */
                                                $current = request()->route('record') ? PrevDog::find(request()->route('record')) : null;
                                                if (!($data['RaceID'] ?? null) && $current) {
                                                    $data['RaceID'] = $current->RaceID;
                                                }

                                                // Temporary SagirID and DataID = max+1
                                                $maxSagir = PrevDog::query()->max('SagirID') ?? 0;
                                                $maxData = PrevDog::query()->max('DataID') ?? 0;
                                                $data['SagirID'] = (int)($maxSagir) + 1;
                                                $data['DataID'] = (int)($maxData) + 1;

                                                // Map optional alias fields from form to model
                                                if (isset($data['breeder_name'])) {
                                                    $data['Breeder_Name'] = $data['breeder_name'];
                                                }

                                                $father = PrevDog::create($data);

                                                return $father->SagirID; // Return the PK used in the select
                                            })
                                            ->createOptionAction(fn(Action $action) => $action
                                                ->modalHeading(__('Create Father'))
                                                ->modalWidth('4xl')
                                            ),

                                        Select::make('MotherSAGIR')
                                            ->label(__('Mother'))
                                            ->columnSpan(2)
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                return PrevDog::query()
                                                    ->where('SagirID', 'like', "%{$search}%")
                                                    ->orWhere('Heb_Name', 'like', "%{$search}%")
                                                    ->orWhere('Eng_Name', 'like', "%{$search}%")
                                                    ->orWhere('Chip', 'like', "%{$search}%")
                                                    ->orWhere('ImportNumber', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn($d) => [$d->SagirID => $d->SagirID . ' - ' . $d->full_name])
                                                    ->all();
                                            })
                                            ->getOptionLabelUsing(function ($value) {
                                                $d = PrevDog::query()->where('SagirID', $value)->first();

                                                return $d ? ($d->SagirID . ' - ' . $d->full_name) : null;
                                            }),
                                        Forms\Components\TextInput::make('sheger_id')
                                            ->label(__('Sheger ID'))
                                            ->numeric(),
                                        Forms\Components\Toggle::make('red_pedigree')
                                            ->label(__('Red Pedigree'))
                                            ->inline(false)
                                            ->onColor('danger')
                                            ->offColor('gray'),
                                        Forms\Components\ToggleButtons::make('pedigree_color')
                                            ->label(__('Pedigree Color'))
                                            ->options(LegacyPedigreeColor::class)
                                            ->grouped(),
                                        Forms\Components\Textarea::make('PedigreeNotes')
                                            ->label(__('Pedigree Notes'))
                                            ->maxLength(4000)
                                            ->columnSpan(2),
                                        Forms\Components\Textarea::make('PedigreeNotes_2')
                                            ->label(__('Pedigree Notes (2)'))
                                            ->maxLength(1000)
                                            ->columnSpan(2),
                                    ])
                                    ->heading(__('Pedigree'))
                                    ->columns(4),
                                Section::make('ancestry')
                                    ->schema([
                                        Forms\Components\Placeholder::make('pedigree_placeholder')
                                            ->content(new HtmlString(__('A 3-generation pedigree view will appear here soon.'))),
                                    ])
                                    ->heading(__('Ancestry (coming soon)')),
                            ])
                            ->label(__('Pedigree')),

                        FormTab::make('Shows')
                            ->schema([
                                Forms\Components\TextInput::make('ShowsCount')
                                    ->label(__('Shows Count'))
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\Placeholder::make('shows_hint')
                                    ->content(new HtmlString(__('Shows list will be available via a relation manager.')))
                                    ->columnSpanFull(),
                            ])
                            ->label(__('Shows'))
                            ->columns(4),

                        FormTab::make('Documents')
                            ->schema([
                                Forms\Components\Placeholder::make('documents_hint')
                                    ->content(new HtmlString(__('Documents management is coming soon.')))
                                    ->columnSpanFull(),
                                Section::make('media')
                                    ->schema([
                                        Forms\Components\TextInput::make('ProfileImage')
                                            ->label(__('Profile Image'))
                                            ->maxLength(300),
                                        Forms\Components\TextInput::make('Image2')
                                            ->label(__('Image 2'))
                                            ->maxLength(300),
                                    ])
                                    ->heading(__('Media'))
                                    ->columns(2),
                            ])
                            ->label(__('Documents')),

                        FormTab::make('health')
                            ->schema([
                                Section::make(__('health_identification'))
                                    ->schema([
                                        Forms\Components\TextInput::make('Pelvis')
                                            ->label(__('Pelvis'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('SCH')
                                            ->label(__('SCH'))
                                            ->numeric(),
                                        Forms\Components\Textarea::make('HealthNotes')
                                            ->label(__('Health Notes'))
                                            ->maxLength(4000)
                                            ->columnSpan(2),
                                    ])
                                    ->heading(__('Health & Identification'))
                                    ->columns(4),
                                Section::make('mag')
                                    ->schema([
                                        Forms\Components\TextInput::make('IsMagPass')
                                            ->label(__('MAG Pass'))
                                            ->numeric(),
                                        Forms\Components\DatePicker::make('MagDate')
                                            ->label(__('MAG Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('yyyy-mm-dd')
                                            ->displayFormat('d-m-Y')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Forms\Components\TextInput::make('MagJudge')
                                            ->label(__('MAG Judge'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('MagPlace')
                                            ->label(__('MAG Place'))
                                            ->maxLength(200),
                                        Forms\Components\TextInput::make('IsMagPass_2')
                                            ->label(__('MAG 2nd Pass'))
                                            ->numeric(),
                                        Forms\Components\DatePicker::make('MagDate_2')
                                            ->label(__('MAG 2nd Date'))
                                            ->timezone('Asia/Jerusalem')
                                            ->native(false)
                                            ->locale('he')
                                            ->format('yyyy-mm-dd')
                                            ->displayFormat('d-m-Y')
                                            ->weekStartsOnSunday()
                                            ->closeOnDateSelection(),
                                        Forms\Components\TextInput::make('MagJudge_2')
                                            ->label(__('MAG 2nd Judge'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('MagPlace_2')
                                            ->label(__('MAG 2nd Place'))
                                            ->maxLength(255),
                                    ])
                                    ->heading(__('MAG'))
                                    ->columns(4),
                            ])
                            ->label(__('Health')),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with('breed')
                    ->with('color')
                    ->with('hair')
                    ->with('father')
                    ->with('mother')
                    ->with('owners')
                    ->with('titles')
                    ->with('duplicates');
                //                ->with('currentOwner');
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('id'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('duplicates_count')
                    ->label(__('Duplicates Count'))
                    ->numeric()
                    ->counts('duplicates')
                    ->sortable(['duplicates_count'])
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('duplicates')
                    ->label(__('Other Duplicate IDs'))
                    ->formatStateUsing(function (PrevDog $record): HtmlString {
                        // format the related duplicates so each of the duplicates array items will be a link to the route of PrevDogResource view page using the id as the parameter
                        $duplicatesLinks = $record->duplicates?->pluck('id')
                            ->filter(fn ($id) => $id != $record->id)
                            ->map(fn($id) => '<a href="' . PrevDogResource::getUrl('view', ['record' => $id]) . '" target="_blank">' . $id . '</a>'
                            )
                            ->implode(', ');

                        return new HtmlString($duplicatesLinks);
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->prefix(fn(PrevDog $record): string => ($record->sagir_prefix?->code() ?? 'NUL') . ' | ')
                    ->copyable()
                    ->copyMessageDuration(duration: 1200)
                    ->copyMessage(fn($state): string => __('Copied Sagir: :id', ['id' => $state]))
                    ->searchable(['SagirID'], isIndividual: true, isGlobal: false)
                    ->sortable(['SagirID']),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable(['Heb_Name', 'Eng_Name'], isIndividual: true, isGlobal: false),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->description(function (PrevDog $record): string {
                        return $record->breed?->BreedNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['BreedName'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('color.ColorNameHE')
                    ->label(__('Color'))
                    ->description(function (PrevDog $record): string {
                        return $record->color?->ColorNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['ColorNameHE'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('hair.HairNameHE')
                    ->label(__('Hair'))
                    ->description(function (PrevDog $record): string {
                        return $record->hair?->HairNameEN ?? '~';
                    }, position: 'under')
                    ->sortable(['HairNameHE'])
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('GenderID')
                    ->label(__('Gender'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $g = $state instanceof LegacyDogGender
                            ? $state
                            : (isset($state) ? LegacyDogGender::tryFrom((int)$state) : null);

                        return $g?->getLabel();
                    })
                    ->color(function ($state) {
                        $g = $state instanceof LegacyDogGender
                            ? $state
                            : (isset($state) ? LegacyDogGender::tryFrom((int)$state) : null);

                        return $g?->getColor();
                    })
                    ->icon(function ($state) {
                        $g = $state instanceof LegacyDogGender
                            ? $state
                            : (isset($state) ? LegacyDogGender::tryFrom((int)$state) : null);

                        return $g?->getIcon();
                    })
                    ->description(fn(PrevDog $r) => !empty($r->Sex) ? "({$r->Sex})" : null, position: 'under')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Sex')
                    ->label(__('Sex'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('RegDate')
                    ->label(__('Registration Date'))
                    ->date()
                    ->sinceTooltip()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('titles.name')
                    ->label(__('Titles'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->tooltip(fn(Tables\Columns\TextColumn $column): ?string => (($state = $column->getState()) === null) ? null :
                        (is_array($state)
                            ? (count($state) > $column->getListLimit() ? implode(' | ', $state) : null)
                            : (string) $state
                        )
                    )
                    ->searchable(['dogs_titles_db.TitleName'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('owners.full_name')
                    ->label(__('Owners'))
                    ->description(function (PrevDog $record): string {
                        // Get the first two owners' names
                        return $record->owners?->pluck('id')->implode(', ');
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('father.full_name')
                    ->label(__('Father'))
                    ->description(function (PrevDog $record): string {
                        return $record->father?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name', 'Heb_Name', 'SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('mother.full_name')
                    ->label(__('Mother'))
                    ->description(function (PrevDog $record): string {
                        return $record->mother?->SagirID ?? 'n/a';
                    }, position: 'under')
                    ->searchable(['Eng_Name', 'Heb_Name', 'SagirID'], isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Chip')
                    ->label(__('Chip'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('DnaID')
                    ->label(__('DNA ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('ImportNumber')
                    ->label(__('Import Number'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('Chip_2')
                    ->label(__('Chip 2'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BeitGidulID')
                    ->label(__('Beit Gidul ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BeitGidulName')
                    ->label(__('Beit Gidul'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GrowerId')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                //                Tables\Columns\TextColumn::make('CurrentOwnerId')
                //                    ->label(__('Owner (depracted)'))
                //                    ->wrapHeader()
                //                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                //                    ->formatStateUsing(function (PrevDog $record): string {
                //                        if ($record->CurrentOwnerId === null) {
                //                            return 'n/a';
                //                        }
                //                        $ownerName = $record->currentOwner?->first_name ?? '<unknown>';
                //                        $ownershipDate = $record->OwnershipDate
                //                            ? \Carbon\Carbon::parse($record->OwnershipDate)->format('d-m-Y')
                //                            : 'n/a';
                //                        $ownershipDetails = $ownerName . ' | ' . $ownershipDate;
                //                        return $ownershipDetails;
                //                    })
                //                    ->description(fn (PrevDog $record): string => $record->CurrentOwnerId ? (string)((int)$record->CurrentOwnerId) : 'n/a')
                //                    ->sortable(['CurrentOwnerId'])
                //                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Breeder_Name')
                    ->label(__('Breeder Name - depracted'))
                    ->wrapHeader()
                    ->sortable()
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Foreign_Breeder_name')
                    ->label(__('Foreign Breeder'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Breeding_ManagerID')
                    ->label(__('Breeding Manager ID - check'))
                    ->wrapHeader()
                    ->description(fn (PrevDog $record): string => $record->breedingManager->full_name ?? 'n/a')
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Status')
                    ->label(__('Status'))
                    ->badge()
                    ->icon(fn(PrevDog $record): string => $record->Status?->getIcon() ?? 'fas-minus-circle')
                    ->color(fn(PrevDog $record): string => $record->Status?->getColor() ?? 'gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('BreedID')
                    ->label(__('Breed ID - depracted'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SizeID')
                    ->label(__('Size'))
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ShowsCount')
                    ->label(__('Shows Count - check'))
                    ->wrapHeader()
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Pelvis')
                    ->label(__('Pelvis'))
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('SCH')
                    ->label(__('SCH'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('RemarkCode')
                    ->label(__('Remark Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('TitleName')
                    ->label(__('Titles (pre ~2010)'))
                    ->wrapHeader()
                    ->separator(',')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GroupID')
                    ->label(__('Group ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('GidulShowType')
                    ->label(__('Gidul Show'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pedigree_color')
                    ->label(__('Pedigree Color'))
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $c = $state instanceof LegacyPedigreeColor
                            ? $state
                            : (!is_null($state) ? LegacyPedigreeColor::tryFrom((string)$state) : null);

                        return $c?->getLabel();
                    })
                    ->color(function ($state) {
                        $c = $state instanceof LegacyPedigreeColor
                            ? $state
                            : (!is_null($state) ? LegacyPedigreeColor::tryFrom((string)$state) : null);

                        return $c?->getColor();
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('red_pedigree')
                    ->label(__('Red Pedigree'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('PedigreeNotes')
                    ->label(__('Pedigree Notes'))
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('PedigreeNotes_2')
                    ->label(__('Pedigree Notes 2'))
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('HealthNotes')
                    ->label(__('Health Notes'))
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Notes_2')
                    ->label(__('Notes 2'))
                    ->limit(200)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('message_test')
                    ->label(__('Message Test'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sheger_id')
                    ->label(__('Sheger ID'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag columns into one column, pass will be the value/state and the rest in a description
                Tables\Columns\TextColumn::make('IsMagPass')
                    ->label(__('Mag'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate ?? '~';
                        $magJudge = $record->MagJudge ?? '~';
                        $magPlace = $record->MagPlace ?? '~';

                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // combine the Mag 2 columns into one column, pass will be the value/state and the rest in a description
                Tables\Columns\TextColumn::make('IsMagPass_2')
                    ->label(__('Mag 2'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->description(function (PrevDog $record): string {
                        $magDate = $record->MagDate_2 ?? '~';
                        $magJudge = $record->MagJudge_2 ?? '~';
                        $magPlace = $record->MagPlace_2 ?? '~';

                        return "{$magDate} | {$magJudge} | {$magPlace}";
                    }, position: 'under')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('is_correct')
                    ->label(__('Is Correct'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ProfileImage')
                    ->label(__('Profile Image'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Image2')
                    ->label(__('Profile Image 2'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('not_relevant')
                    ->label(__('Not Relevant'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('encoding')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('ModificationDateTime')
                    ->label(__('Modification Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('CreationDateTime')
                    ->label(__('Creation Date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Filter::make('trashed')
                    ->form([
                        Forms\Components\ToggleButtons::make('trashed')
                            ->label(__('Trashed'))
                            ->options([
                                'not_deleted' => 'Not Deleted',
                                'deleted' => 'Deleted',
                                'all' => 'All',
                            ])
                            ->colors([
                                'not_deleted' => 'success',
                                'deleted' => 'danger',
                                'all' => 'gray',
                            ])
                            ->default('not_deleted')
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['trashed']) || $data['trashed'] === 'all') {
                            return $query;
                        }

                        return match ($data['trashed']) {
                            'deleted' => $query->onlyTrashed(),
                            'not_deleted' => $query->withoutTrashed(),
                        };
                    }),

                Filter::make('GenderID')
                    ->label(__('Gender'))
                    ->form([
                        Forms\Components\ToggleButtons::make('GenderID')
                            ->label(__('Gender'))
                            ->options(LegacyDogGender::class)
                            ->colors([
                                1 => 'blue',
                                2 => 'pink',
                            ])
                            ->icons([
                                1 => 'fas-mars',
                                2 => 'fas-venus',
                            ])
                            ->inline()
                            ->grouped()
                            ->nullable(),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        filled($data['GenderID'] ?? null),
                        fn(Builder $q): Builder => $q->where('GenderID', $data['GenderID'])
                    )),
                Filter::make('sagir_prefix')
                    ->form([
                        Forms\Components\ToggleButtons::make('sagir_prefix')
                            ->label(__('Sagir Prefix'))
                            ->options(LegacySagirPrefix::class)
                            ->grouped(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        // If no specific prefix is chosen, return unfiltered results.
                        if (empty($data['sagir_prefix'])) {
                            return $query;
                        }

                        return $query->where('sagir_prefix', $data['sagir_prefix']);
                    }),
                Tables\Filters\SelectFilter::make('breed')
                    ->label(__('Breed'))
                    ->relationship('breed', 'BreedName')
                    ->multiple()
                    ->searchable(['BreedName', 'BreedNameEN'])
                    ->getOptionLabelFromRecordUsing(fn(PrevBreed $record): string => $record->BreedName . ' | ' . $record->BreedNameEN),
                Tables\Filters\SelectFilter::make('color')
                    ->label(__('Color'))
                    ->relationship('color', 'ColorNameHE')
                    ->multiple()
                    ->searchable(['ColorNameHE', 'ColorNameEN'])
                    ->getOptionLabelFromRecordUsing(fn(PrevColor $record): string => $record->ColorNameHE . ' | ' . $record->ColorNameEN),
                Tables\Filters\SelectFilter::make('hair')
                    ->label(__('Hair'))
                    ->relationship('hair', 'HairNameHE')
                    ->multiple()
                    ->searchable(['HairNameHE', 'HairNameEN'])
                    ->getOptionLabelFromRecordUsing(fn(PrevHair $record): string => $record->HairNameHE . ' | ' . $record->HairNameEN),
                // Combined filter for Father (searching by Hebrew Name, English Name or SagirID)
                Filter::make('father')
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['father_search'])) {
                            $query->whereHas('father', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['father_search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['father_search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['father_search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('father_search')
                            ->label(__('Father'))
                            ->hint('Name \ Sagir')
                            ->helperText('Search by Hebrew\English Name or Sagir'),
                    ]),
                // Combined filter for Mother (searching by Hebrew Name, English Name or SagirID)
                Filter::make('mother')
                    ->label(__('Mother'))
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['mother_search'])) {
                            $query->whereHas('mother', function (Builder $query) use ($data) {
                                $query->where('Heb_Name', 'like', "%{$data['mother_search']}%")
                                    ->orWhere('Eng_Name', 'like', "%{$data['mother_search']}%")
                                    ->orWhere('SagirID', 'like', "%{$data['mother_search']}%");
                            });
                        }
                    })
                    ->form([
                        Forms\Components\TextInput::make('mother_search')
                            ->label(__('Mother'))
                            ->hint('Name \ Sagir')
                            ->helperText('Search by Hebrew\English Name or Sagir'),
                    ]),
                // create filters to select and search by "owners" (PrevUser many 2 many relationship) fields: first_name, last_name, first_name_en, last_name_en, mobile_phone, id and custom attributes: full_name, name - owners is a relationship, full_name is a custom accessor using: ["first_name", "last_name", "first_name_en", "last_name_en"]
                Tables\Filters\SelectFilter::make('owners')
                    ->label(__('Owners'))
                    ->multiple()
                    ->relationship('owners', 'id') // Defines the relationship to query against
                    ->searchable(false) // We provide a custom search, so disable the default

                    // What to do when the user types in the search box
                    ->getSearchResultsUsing(
                        fn (?string $search): array => PrevUser::selectOptions($search)
                    )

                    // How to get labels for already-selected options when the form loads
                    ->getOptionLabelsUsing(
                        fn (array $values): array => PrevUser::whereIn('id', $values)->get()->pluck('search_label', 'id')->toArray()
                    )

                    // This is ALREADY handled by ->relationship(), but left for clarity
                    // on how to apply the final filter to the main table query.
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->whereHas('owners', fn(Builder $q): Builder => $q->whereIn('users.id', $data['values']));
                    }),

                // boolean filters for: IsMagPass, IsMagPass_2, not_relevant, red_pedigree
                // Tables\Filters\TernaryFilter::make('red_pedigree')
                //     ->label(__('Red Pedigree'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('red_pedigree', 1),
                //         false: fn (Builder $query) => $query->whereNot('red_pedigree', 1),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Tables\Filters\TernaryFilter::make('IsMagPass')
                //     ->label(__('MHG Pass'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('IsMagPass', 1),
                //         false: fn (Builder $query) => $query->where('IsMagPass', 0),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Tables\Filters\TernaryFilter::make('IsMagPass_2')
                //     ->label(__('MHG 2nd Pass'))
                //     ->placeholder('All')
                //     ->trueLabel('Yes')
                //     ->falseLabel('No')
                //     ->queries(
                //         true: fn (Builder $query) => $query->where('IsMagPass_2', 1),
                //         false: fn (Builder $query) => $query->where('IsMagPass_2', 0),
                //         blank: fn (Builder $query) => $query,
                //     ),
                // Date filters for RegDate, BirthDate, OwnershipDate
                Filter::make('RegDate')
                    ->form([
                        DatePicker::make('RegDate')
                            ->label(__('Registration Date'))
                            ->timezone('Asia/Jerusalem')
                            ->native(false)
                            ->locale('he')
                            ->format('yyyy-mm-dd')
                            ->displayFormat('d-m-Y')
                            ->weekStartsOnSunday()
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['RegDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('RegDate', '>=', $date)
                        );
                    }),
                Filter::make('BirthDate')
                    ->form([
                        DatePicker::make('BirthDate')
                            ->label(__('Birth Date'))
                            ->timezone('Asia/Jerusalem')
                            ->native(false)
                            ->locale('he')
                            ->format('yyyy-mm-dd')
                            ->displayFormat('d-m-Y')
                            ->weekStartsOnSunday()
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['BirthDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('BirthDate', '>=', $date)
                        );
                    }),
                Filter::make('OwnershipDate')
                    ->form([
                        DatePicker::make('OwnershipDate')
                            ->label(__('Ownership Date'))
                            ->timezone('Asia/Jerusalem')
                            ->native(false)
                            ->locale('he')
                            ->format('yyyy-mm-dd')
                            ->displayFormat('d-m-Y')
                            ->weekStartsOnSunday()
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['OwnershipDate'],
                            fn (Builder $query, $date): Builder => $query->whereDate('OwnershipDate', '>=', $date)
                        );
                    }),
                Tables\Filters\TernaryFilter::make('duplicates_count')
                    ->label(__('Duplicates'))
                    ->placeholder('All')
                    ->trueLabel('Duplicates')
                    ->falseLabel('No Duplicates')
                    ->queries(
                        true: fn (Builder $query) => $query->has('duplicates', '>', 1),
                        false: fn (Builder $query) => $query->has('duplicates', '<=', 1),
                        blank: fn (Builder $query) => $query,
                    ),

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([5, 10, 15, 25, 50, 100, 200, 250, 300])
            ->defaultPaginationPageOption(15)
            ->defaultSort('SagirID', 'desc')
            ->searchOnBlur()
            ->striped()
            ->deferLoading()
//          ->recordUrl(false)
            ->recordUrl(fn(PrevDog $record): string => PrevDogResource::getUrl('edit', ['record' => $record]))
            ->recordClasses(fn (Model $record) => $record->trashed() ? 'fi-ta-row-deleted' : null);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Dog Record')->tabs([
                    /***** 1. Overview *****/
                    Tab::make('General')
                        ->schema([
                            InfolistGrid::make(4)->schema([
                                //                            TextEntry::make('sagir_prefix')
                                //                                ->badge()
                                //                                ->color(fn (PrevDog $record): string => $record->sagir_prefix->getColor())
                                //                                ->icon(fn (PrevDog $record): string => $record->sagir_prefix->getIcon()),
                                TextEntry::make('SagirID')
                                    ->label(__('Sagir'))
                                    ->prefix(fn(PrevDog $record): string => $record->sagir_prefix->code())
                                    ->numeric(decimalPlaces: 0, thousandsSeparator: ''),
                                TextEntry::make('full_name')
                                    ->label(__('Full Name')),
                                TextEntry::make('RegDate')
                                    ->label(__('Registration Date'))
                                    ->date(),
                                TextEntry::make('BirthDate')
                                    ->label(__('Birth Date'))
                                    ->date(),
                            ]),
                            InfolistGrid::make(4)->schema([
                                TextEntry::make('GenderID')
                                    ->label(__('Gender'))
                                    ->state(fn(PrevDog $record): string => ($record->GenderID?->getLabel() ?? 'n/a')
                                        . (!empty($record->Sex) ? " ({$record->Sex})" : '')
                                    )
                                    ->color(fn(PrevDog $record) => $record->GenderID?->getColor())
                                    ->icon(fn(PrevDog $record) => $record->GenderID?->getIcon())
                                    ->iconColor(fn(PrevDog $record) => $record->GenderID?->getColor()),
                                TextEntry::make('breed.BreedName')->label(__('Breed')),
                                TextEntry::make('color.ColorNameHE')->label(__('Color')),
                                TextEntry::make('hair.HairNameHE')->label(__('Hair')),
                                TextEntry::make('Status')
                                    ->label(__('Status'))
                                    ->badge()
                                    ->icon(fn(PrevDog $record): string => $record->Status?->getIcon() ?? 'fas-minus-circle')
                                    ->color(fn(PrevDog $record): string => $record->Status?->getColor() ?? 'gray'),
                            ]),
                        ])
                        ->label(__('General')),

                    /***** 2. Ownership & Breeding *****/
                    Tab::make('Ownership & Breeding')->schema([
                        InfolistSection::make('Ownership')->schema([
                            RepeatableEntry::make('owners')
                                ->schema([
                                    TextEntry::make('full_name')->label(__('Full Name')),
                                    TextEntry::make('mobile_phone')->label(__('Phone')),
                                    TextEntry::make('email')->label(__('Email')),
                                ])
                                ->label(fn(PrevDog $record): string => __('Owners') . " ({$record->owners->count()})")
                                ->grid(4),
                            TextEntry::make('currentOwner.full_name')
                                ->label(__('Current Owner')),
                            TextEntry::make('OwnershipDate')
                                ->label(__('Ownership Date')),
                        ])
                            ->label(__('Ownership')),
                        InfolistSection::make('Breeding')->schema([
                            TextEntry::make('Breeder_Name')->label(__('Breeder')),
                            TextEntry::make('Foreign_Breeder_name')->label(__('Foreign Breeder')),
                            TextEntry::make('breedingManager.full_name')->label(__('Breeding Manager')),
                            TextEntry::make('BeitGidulID')->label(__('Beit Gidul ID')),
                        ])
                            ->label(__('Breeding')),
                    ])
                        ->label(__('Ownership & Breeding')),

                    /***** 3. Pedigree & Titles *****/
                    Tab::make('Pedigree & Titles')->schema([
                        InfolistSection::make('Pedigree')->schema([
                            InfolistSection::make('Parants')->schema([
                                InfolistSection::make('Father Details')->schema([
                                    TextEntry::make('father.full_name')->label(__('Father Name')),
                                    TextEntry::make('father.SagirID')->label(__('Father Sagir ID')),
                                ])->columns(2),
                                InfolistSection::make('Mother Details')->schema([
                                    TextEntry::make('mother.full_name')->label(__('Mother Name')),
                                    TextEntry::make('mother.SagirID')->label(__('Mother Sagir ID')),
                                ])->columns(2),
                            ])->columns(2),
                            TextEntry::make('pedigree_color')->label(__('Pedigree Color')),
                            IconEntry::make('red_pedigree')->label(__('Red Pedigree')),
                            TextEntry::make('PedigreeNotes')
                                ->label(__('Pedigree Notes'))
                                ->columnSpanFull(),
                        ])
                            ->label(__('Pedigree')),
                        InfolistSection::make('Titles & Shows')->schema([
                            RepeatableEntry::make('titles')
                                ->label(fn(PrevDog $record): string => __('Titles') . " ({$record->titles->count()})")
                                ->schema([
                                    TextEntry::make('name')
                                        ->hiddenLabel()
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color(Color::Blue)
                                        ->columnSpan(2),
                                    TextEntry::make('awarding.EventPlace')
                                        ->hiddenLabel()
                                        // ->badge()
                                        ->color('warning')
                                        ->size(TextEntry\TextEntrySize::Medium)
                                        ->columnSpan(3),
                                    TextEntry::make('awarding.EventDate')
                                        ->hiddenLabel()
                                        ->date()
                                        ->columnSpan(2),
                                    TextEntry::make('awarding.EventName')
                                        ->hiddenLabel()
                                        ->columnSpan(3),
                                ])
                                ->columns(5)
                                ->grid(5),
                            TextEntry::make('ShowsCount')->label(__('Shows Count')),
                        ])
                            ->label(__('Titles & Shows')),
                        TextEntry::make('TitleName')->label(__('Titles (pre‑2010)')),
                    ])
                        ->label(__('Pedigree & Titles')),

                    /***** 4. Metrics & Performance *****/
                    Tab::make('Metrics & Performance')->schema([
                        InfolistGrid::make(2)->schema([
                            IconEntry::make('IsMagPass')->label(__('MHG Pass')),
                            IconEntry::make('IsMagPass_2')->label(__('MHG 2nd Pass')),
                        ]),
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('SupplementarySign')->label(__('Supplementary Sign')),
                            TextEntry::make('SizeID')->label(__('Size ID')),
                            TextEntry::make('GroupID')->label(__('Group ID')),
                        ]),
                    ]),

                    /***** 5. Health & Notes *****/
                    Tab::make('Health & Notes')->schema([
                        TextEntry::make('HealthNotes')
                            ->label(__('Health Notes'))
                            ->columnSpanFull(),
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('Pelvis')->label(__('Pelvis')),
                            TextEntry::make('SCH')->label(__('SCH')),
                        ]),
                        TextEntry::make('Notes_2')
                            ->label(__('Additional Notes'))
                            ->columnSpanFull(),
                        TextEntry::make('message_test')->label(__('Message Test')),
                    ]),

                    /***** 6. Media & Flags *****/
                    Tab::make('Media')->schema([
                        InfolistGrid::make(2)->schema([
                            ImageEntry::make('ProfileImage')->label(__('Profile Image')),
                            ImageEntry::make('Image2')->label(__('Image 2')),
                        ]),
                    ]),

                    /***** 7. Metadata *****/
                    Tab::make('Metadata')->schema([
                        InfolistGrid::make(5)->schema([
                            TextEntry::make('id')->label(__('ID')),
                            IconEntry::make('not_relevant')->label(__('Not Relevant')),
                            IconEntry::make('encoding')->label(__('Encoding Issue')),
                        ]),
                        InfolistGrid::make(5)->schema([
                            TextEntry::make('CreationDateTime')
                                ->label(__('Created On'))
                                ->date(),
                            TextEntry::make('ModificationDateTime')
                                ->label(__('Modified On'))
                                ->date(),
                            TextEntry::make('created_at')
                                ->label(__('Created At'))
                                ->date(),
                            TextEntry::make('updated_at')
                                ->label(__('Updated At'))
                                ->date(),
                            TextEntry::make('deleted_at')
                                ->label(__('Deleted At'))
                                ->date(),
                        ]),
                    ]),
                ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            PrevDogResource\RelationManagers\OwnersRelationManager::class,
            PrevDogResource\RelationManagers\TitlesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrevDogs::route('/'),
            'create' => Pages\CreatePrevDog::route('/create'),
            'view' => Pages\ViewPrevDog::route('/{record}'),
            'edit' => Pages\EditPrevDog::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PrevDogResource\Widgets\DogStats::class,
        ];
    }
}
