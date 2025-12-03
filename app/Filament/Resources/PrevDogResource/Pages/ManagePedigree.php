<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use App\Services\Legacy\PrevDogService;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;


class ManagePedigree extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = PrevDogResource::class;

    protected static string $view = 'filament.resources.prev-dog-resource.pages.manage-pedigree';

    public function getTitle(): string|Htmlable
    {
        return __('Manage Pedigree');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Pedigree');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('dog/model/general.labels.navigation_group');
    }

    protected static ?string $navigationIcon = 'fas-dna';

    protected static ?int $navigationSort = 99;

    /** Selected root dog by SagirID (the select’s value) */
    public ?int $rootSagir = null;

    /** Loaded root dog model */
    public ?PrevDog $subject = null;

    /** Filament page form state */
    public ?array $data = [];

    public function mount(int|string|null $record): void
    {
        // Start clean to avoid stale state when navigating without a record.
        $this->subject = null;
        $this->rootSagir = null;

        if ($record) {
            // When arriving from a record route, $record is the PK id.
            $this->record = $this->resolveRecord($record)->loadMissing([
                'father:id,SagirID',
                'mother:id,SagirID',
            ]);
            $this->subject = $this->record;
            $this->rootSagir = $this->record->SagirID;
        } else {
            // Ensure InteractsWithRecord always has a Model to keep internals happy (breadcrumbs/title).
            $this->record = PrevDog::make();
        }

        // Prime the top Select.
        $this->form->fill([
            'rootSagir' => $this->rootSagir,
        ]);
    }

    /**
     * Re-hydrate subject/record after user selects or creates a root dog.
     */
    protected function setRootFromSagir(?int $sagir): void
    {
        $this->rootSagir = $sagir ?: null;

        if ($this->rootSagir) {
            $this->subject = PrevDog::with(['breed', 'color', 'hair', 'owners'])->where('SagirID', $this->rootSagir)->first();
            $this->record = $this->subject ?: PrevDog::make();
        } else {
            $this->subject = null;
            $this->record = PrevDog::make();
        }

        $this->form->fill([
            'rootSagir' => $this->rootSagir,
        ]);

        $this->updateUrlToSubject();
    }

    /**
     * Push the proper URL:
     * - With record: /prev-dogs/{id}/pedigree
     * - Without record: /prev-dogs/pedigree
     * Uses Livewire v3 "navigate" to avoid full reload.
     */
    protected function updateUrlToSubject(): void
    {
        $currentId = request()->route('record');                 // may be null
        $targetId = $this->subject?->getKey();                  // may be null

        // avoid needless navigation if already on the right URL
        if (($currentId ?: null) === ($targetId ?: null)) {
            return;
        }

        $url = $this->subject
            ? PrevDogResource::getUrl('pedigree', ['record' => $this->subject])
            : PrevDogResource::getUrl('pedigree');

        // Livewire v3 navigate = pushState, no hard reload
        $this->redirect($url, navigate: true);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->record ?? PrevDog::make())
            ->schema([
                Section::make(__('Select main dog'))
                    ->schema([
                        Grid::make(1)->schema([
                            Select::make('rootSagir')
                                ->label(__('Main Dog'))
                                ->searchable()
                                ->allowHtml()
                                ->loadingMessage(__('Loading :model', ['model' => __('dog/model/general.labels.plural')]) . '...')
                                ->searchingMessage(__('Searching :model', ['model' => __('dog/model/general.labels.plural')]) . '...')
                                ->searchPrompt(__('Search dogs by import number, sagir, chip or name'))
                                ->noSearchResultsMessage(__('No :model found.', ['model' => __('dog/model/general.labels.plural')]))

                                // Search results use formatted_label
                                ->getSearchResultsUsing(function (string $search): array {
                                    return PrevDog::with(['breed'])
                                        ->whereAny([
                                            'ImportNumber',
                                            'SagirID',
                                            'Eng_Name',
                                            'Heb_Name',
                                            'Chip',
                                        ], 'like', "%{$search}%")
                                        ->orderBy('RaceID', 'asc')
                                        ->orderBy('ImportNumber', 'desc')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn(PrevDog $d) => [
                                            $d->SagirID => (string)$d->formatted_label,
                                        ])
                                        ->all();
                                })

                                // Current value label (when the select has a value)
                                ->getOptionLabelUsing(function ($value): ?string {
                                    if (!$value) {
                                        return null;
                                    }

                                    $dog = PrevDog::query()
                                        ->with(['breed'])
                                        ->where('SagirID', $value)
                                        ->first();

                                    return $dog?->formatted_label ?? (string)$value;
                                })

                                // -------- CREATE OPTION (visible only when empty) --------
                                ->createOptionForm([
                                    Grid::make(12)->schema([
                                        // Left: identity
                                        Group::make([
                                            Forms\Components\TextInput::make('Eng_Name')
                                                ->label(__('Name (EN)'))
                                                ->live(debounce: 1000)
                                                ->required()
                                                ->string()
                                                ->maxLength(200),

                                            Forms\Components\TextInput::make('Heb_Name')
                                                ->label(__('Name (HE)'))
                                                ->live(debounce: 1000)
                                                ->string()
                                                ->maxLength(200),

                                            // Gender ToggleButtons (match your resource style as close as possible)
                                            ToggleButtons::make('GenderID')
                                                ->label(__('Gender'))
                                                ->live()
                                                ->inline()
                                                ->options(LegacyDogGender::class)
                                                ->required()
                                                ->rules(['in:1,2'])
                                            ,
                                        ])->columnSpan(6),

                                        // Right: identifiers
                                        Group::make([
                                            Forms\Components\TextInput::make('ImportNumber')
                                                ->label(__('Import #'))
                                                ->live(debounce: 1000)
                                                ->required()
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'ImportNumber', ignoreRecord: true),

                                            Forms\Components\TextInput::make('Chip')
                                                ->label(__('Chip'))
                                                ->live(debounce: 1000)
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'Chip', ignoreRecord: true),

                                            Forms\Components\TextInput::make('DnaID')
                                                ->label(__('DNA ID'))
                                                ->live(debounce: 1000)
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'DnaID', ignoreRecord: true),

                                            Forms\Components\TextInput::make('BirthDate')
                                                ->label(__('Birth Date'))
                                                ->live(debounce: 1000)
                                                ->required()
                                                ->rule('date')
                                                ->rule('date_format:Y-m-d')
                                                ->rule('before_or_equal:today'),
                                        ])->columnSpan(6),

                                        // Relations: Breed, Color, Hair, Owners (multiple)
                                        Fieldset::make(__('Relations'))
                                            ->schema([
                                                Select::make('RaceID')
                                                    ->label(__('Breed'))
                                                    ->relationship('breed', 'BreedName')
                                                    ->searchable(['BreedName', 'BreedNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->required()
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Breeds')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Breeds')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Breeds')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Breeds')])),

                                                Select::make('ColorID')
                                                    ->label(__('Color'))
                                                    ->relationship('color', 'ColorNameHE')
                                                    ->searchable(['ColorNameHE', 'ColorNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Colors')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Colors')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Colors')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Colors')])),

                                                Select::make('HairID')
                                                    ->label(__('Hair'))
                                                    ->relationship('hair', 'HairNameHE')
                                                    ->searchable(['HairNameHE', 'HairNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Hairs')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Hairs')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Hairs')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Hairs')])),

                                                Select::make('owners')
                                                    ->label(__('Owners'))
                                                    ->multiple()
                                                    ->relationship('owners', 'first_name')
                                                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Owners')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Owners')]) . '...')
                                                    ->searchPrompt(__('Search owners by name'))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Owners')])),
                                            ])
                                            ->columns(2)
                                            ->columnSpan(12),
                                    ]),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    /** @var PrevDogService $svc */
                                    $svc = app(PrevDogService::class);

                                    // Service will create with safe SagirID/DataID
                                    $dog = $svc->createMinimalParent(
                                        data: [
                                            'Eng_Name' => $data['Eng_Name'] ?? null,
                                            'Heb_Name' => $data['Heb_Name'] ?? null,
                                            'ImportNumber' => $data['ImportNumber'] ?? null,
                                            'Chip' => $data['Chip'] ?? null,
                                            'DnaID' => $data['DnaID'] ?? null,
                                            'BirthDate' => $data['BirthDate'] ?? null,
                                            'RaceID' => $data['RaceID'] ?? null,
                                            'ColorID' => $data['ColorID'] ?? null,
                                            'HairID' => $data['HairID'] ?? null,
                                        ],
                                        gender: $data['GenderID'] ?? null,
                                    );

                                    if (!empty($data['owners']) && is_array($data['owners'])) {
                                        $dog->owners()->sync($data['owners']); // pivot safe
                                    }

                                    // Return Select value (SagirID).
                                    return (int)$dog->SagirID;
                                })
                                ->createOptionAction(
                                    fn(Action $action) => $action
                                        ->visible(fn(Get $get) => blank($get('rootSagir')))
                                        ->modalHeading(__('Create new dog'))
                                        ->modalWidth('6xl')
                                        ->color('warning')
                                        ->icon('fas-circle-plus')
                                )

                                // -------- EDIT OPTION (visible only when a dog is selected) --------
                                ->editOptionForm([
                                    Grid::make(12)->schema([
                                        Group::make([
                                            Forms\Components\TextInput::make('Eng_Name')
                                                ->label(__('Name (EN)'))
                                                ->live(debounce: 1000)
                                                ->required()
                                                ->string()
                                                ->maxLength(200),

                                            Forms\Components\TextInput::make('Heb_Name')
                                                ->label(__('Name (HE)'))
                                                ->live(debounce: 1000)
                                                ->string()
                                                ->maxLength(200),

                                            ToggleButtons::make('GenderID')
                                                ->label(__('Gender'))
                                                ->live()
                                                ->inline()
                                                ->options(LegacyDogGender::class)
                                                ->required(),
                                        ])->columnSpan(6),

                                        Group::make([
                                            Forms\Components\TextInput::make('ImportNumber')
                                                ->label(__('Import #'))
                                                ->live(debounce: 1000)
                                                ->required()
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'ImportNumber', ignorable: $this->subject),

                                            Forms\Components\TextInput::make('Chip')
                                                ->label(__('Microchip'))
                                                ->live(debounce: 1000)
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'Chip', ignorable: $this->subject),

                                            Forms\Components\TextInput::make('DnaID')
                                                ->label(__('DNA'))
                                                ->live(debounce: 1000)
                                                ->maxLength(50)
                                                ->unique(PrevDog::class, 'DnaID', ignorable: $this->subject),

                                            Forms\Components\DatePicker::make('BirthDate')
                                                ->label(__('Birth date (YYYY-MM-DD)'))
                                                ->required()
                                                ->rule('date_format:Y-m-d')
                                                ->rule('before_or_equal:today'),
                                        ])->columnSpan(6),

                                        Fieldset::make(__('Relations'))
                                            ->schema([
                                                Select::make('RaceID')
                                                    ->label(__('Breed'))
                                                    ->relationship('breed', 'BreedName')
                                                    ->searchable(['BreedName', 'BreedNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->required()
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Breeds')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Breeds')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Breeds')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Breeds')])),

                                                Select::make('ColorID')
                                                    ->label(__('Color'))
                                                    ->relationship('color', 'ColorNameHE')
                                                    ->searchable(['ColorNameHE', 'ColorNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Colors')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Colors')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Colors')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Colors')])),

                                                Select::make('HairID')
                                                    ->label(__('Hair'))
                                                    ->relationship('hair', 'HairNameHE')
                                                    ->searchable(['HairNameHE', 'HairNameEN'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Hairs')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Hairs')]) . '...')
                                                    ->searchPrompt(__('Search :model', ['model' => __('Hairs')]))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Hairs')])),

                                                Select::make('owners')
                                                    ->label(__('Owners'))
                                                    ->multiple()
                                                    ->relationship('owners', 'first_name')
                                                    ->searchable(['first_name', 'last_name', 'first_name_en', 'last_name_en', 'mobile_phone'])
                                                    ->optionsLimit(50)
                                                    ->native(false)
                                                    ->loadingMessage(__('Loading :model', ['model' => __('Owners')]) . '...')
                                                    ->searchingMessage(__('Searching :model', ['model' => __('Owners')]) . '...')
                                                    ->searchPrompt(__('Search owners by name'))
                                                    ->noSearchResultsMessage(__('No :model found.', ['model' => __('Owners')])),
                                            ])
                                            ->columns(2)
                                            ->columnSpan(12),
                                    ]),
                                ])
                                ->updateOptionUsing(function (Model $record, array $data): void {
                                    $record->fill($data);
                                    $record->save();
                                })
                                ->editOptionAction(
                                    fn(Action $action) => $action
                                        ->modalHeading(__('Edit dog'))
                                        ->modalWidth('6xl')
                                        ->color('warning')
                                        ->icon('fas-pen-to-square')
//                                        ->mountUsing(function (Form $form, Get $get): void {
//                                            $record = PrevDog::query()
//                                                ->where('SagirID', (int)$get('rootSagir'))
//                                                ->first();
//
//                                            $form->model($record ?? PrevDog::make())
//                                                ->fill($record?->attributesToArray() ?? []);
//                                        })
                                )

                                // When user selects / clears value (including after create), hydrate page props.
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state) {
                                    $this->setRootFromSagir($state ? (int)$state : null);
                                })
                                ->native(false),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

}
