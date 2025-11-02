<?php

namespace App\Livewire\Legacy\Pedigree;

use App\Enums\Legacy\LegacyDogGender;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Models\PrevDog;
use App\Services\Legacy\PrevDogService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ParentsPairForm extends Component implements HasForms
{
    use InteractsWithForms;

    /** Subject dog (PK id) */
    public int $subjectId;

    /** Current depth (1=root) and max depth */
    public int $depth = 1;

    public int $maxDepth = 4;

    /** Whether to render children (expanded) */
    public bool $expanded = false;

    /** Backing state for this form. */
    public ?array $data = [];

    public PrevDog $subject;

    public function mount(int $subjectId, int $depth = 1, int $maxDepth = 4, bool $expanded = false): void
    {
        $this->subjectId = $subjectId;
        $this->depth = $depth;
        $this->maxDepth = $maxDepth;
        $this->expanded = $expanded;

        $this->subject = PrevDog::query()
            ->with(['father', 'mother'])
            ->findOrFail($this->subjectId);

        $this->form->fill([
            'FatherSAGIR' => $this->subject->FatherSAGIR,
            'MotherSAGIR' => $this->subject->MotherSAGIR,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->subject)
            ->schema([
                Section::make($this->headingForDepth($this->subject))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                $this->parentSelect(
                                    relation: 'father',
                                    field: 'FatherSAGIR',
                                    label: __('Father (Sire)'),
                                    genderForCreate: LegacyDogGender::Male
                                ),
                                $this->parentSelect(
                                    relation: 'mother',
                                    field: 'MotherSAGIR',
                                    label: __('Mother (Dam)'),
                                    genderForCreate: LegacyDogGender::Female
                                ),
                            ]),
                        Actions::make([
                            Action::make('expand')
                                ->label(__('Add Parents'))
                                ->icon('heroicon-m-plus')
                                ->visible(fn() => $this->canExpand())
                                ->action(function () {
                                    $this->expanded = true;
                                }),
                        ])->maxWidth('md'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function canExpand(): bool
    {
        if ($this->depth >= $this->maxDepth) {
            return false;
        }

        return filled($this->subject->FatherSAGIR) || filled($this->subject->MotherSAGIR);
    }

    protected function parentSelect(string $relation, string $field, string $label, LegacyDogGender $genderForCreate): Select
    {
        return Select::make($field)
            ->label($label)
            ->searchable(['SagirID', 'Heb_Name', 'Eng_Name', 'Chip', 'ImportNumber'])
            ->relationship($relation, 'SagirID', modifyQueryUsing: fn(Builder $query) => $query->with(['breed'])->where('GenderID', '=', $genderForCreate->value)->orderBy('ImportNumber', 'desc'), ignoreRecord: true)
            ->optionsLimit(50)
            ->allowHtml()
            ->searchDebounce(2000)
            ->getOptionLabelFromRecordUsing(fn(PrevDog $record) => $record->formatted_label)
            ->manageOptionForm([
                Grid::make(3)
                    ->schema([
                        TextInput::make('ImportNumber')
                            ->label(__('Import Number'))
                            ->unique(PrevDog::class, 'ImportNumber', ignoreRecord: true)
                            ->maxLength(200)
                            ->suffixAction(
                                Action::make('search_import_number')
                                    ->label(__('Search :model', ['model' => __('Import Number')]))
                                    ->icon('fas-search')
                                    ->color('warning')
                                    ->modalHeading(__('Search :model', ['model' => __('Import Number')]))
                                    ->modalContent(function ($state): HtmlString {
                                        $dogs = PrevDog::with(['breed'])
                                            ->whereLike('ImportNumber', "%$state%")
                                            ->limit(50)
                                            ->get();
                                        $formattedData = $dogs->map(fn($dog) => '<li>' . $dog->formatted_label . '</li>')->implode('');
                                        return new HtmlString(
                                            '<ul style="list-style-type: disc; padding-left: 20px;">' .
                                            $formattedData .
                                            '</ul>'
                                        );
                                    })
                            ),
                        TextInput::make('Eng_Name')
                            ->label(__('English Name'))
                            ->maxLength(200),
                        TextInput::make('Heb_Name')
                            ->label(__('Hebrew Name'))
                            ->maxLength(200),
                        Group::make([
                            DatePicker::make('BirthDate')
                                ->label(__('Birth Date'))
                                ->timezone('Asia/Jerusalem')
                                ->native(false)
                                ->locale('he')
                                ->format('Y-m-d')
                                ->displayFormat('Y-m-d')
                                ->weekStartsOnSunday()
                                ->closeOnDateSelection(),
                            DatePicker::make('RegDate')
                                ->label(__('Registration Date'))
                                ->timezone('Asia/Jerusalem')
                                ->native(false)
                                ->locale('he')
                                ->format('Y-m-d')
                                ->displayFormat('Y-m-d')
                                ->weekStartsOnSunday()
                                ->closeOnDateSelection()
                                ->default(now()),
                        ])
                            ->columns(3)
                            ->columnSpan(3),
                        Select::make('RaceID')
                            ->label(__('Breed'))
                            ->relationship('breed', 'BreedName')
                            ->searchable(['BreedName', 'BreedNameEN'])
                            ->preload()
                            ->default(fn() => $this->subject->RaceID),
                        Select::make('ColorID')
                            ->label(__('Color'))
                            ->relationship('color', 'ColorNameHE')
                            ->searchable(['ColorNameHE', 'ColorNameEN'])
                            ->preload()
                            ->default(9000),
                        Select::make('HairID')
                            ->label(__('Hair'))
                            ->relationship('hair', 'HairNameHE')
                            ->searchable(['HairNameHE', 'HairNameEN'])
                            ->preload()
                            ->default(4),
                        TextInput::make('Chip')
                            ->label(__('Chip'))
                            ->unique(PrevDog::class, 'Chip', ignoreRecord: true)
                            ->maxLength(200),
                        TextInput::make('DnaID')
                            ->label(__('DNA ID'))
                            ->unique(PrevDog::class, 'DnaID', ignoreRecord: true)
                            ->maxLength(200),
                        TextInput::make('Breeder_Name')
                            ->label(__('Breeder Name'))
                            ->maxLength(300),
                        Textarea::make('HealthNotes')
                            ->label(__('Health Notes'))
                            ->maxLength(4000),
                        Textarea::make('Notes')
                            ->label(__('Notes'))
                            ->maxLength(1000),
                        TextInput::make('PedigreeNotes')
                            ->label(__('Titles (Pedigree Notes)'))
                            ->helperText('Comma separated'),
                        Select::make('sagir_prefix')
                            ->label(__('Sagir Prefix'))
                            ->options(LegacySagirPrefix::class)
                            ->default(LegacySagirPrefix::NUL->value),
                        Hidden::make('GenderID')->default(fn(Get $get) => $genderForCreate->value),
                    ]),
            ])
            ->createOptionUsing(function (array $data) use ($genderForCreate) {
                /** @var PrevDogService $svc */
                $svc = app(PrevDogService::class);
                $created = $svc->createMinimalParent(
                    data: $data,
                    gender: $genderForCreate,
                    inheritFrom: $this->subject,
                );

                return $created->SagirID; // field stores SagirID
            })
            ->createOptionAction(function (Action $action) use ($genderForCreate, $field) {
                return $action
                    ->visible(fn(Get $get) => blank($get($field)))
                    ->modalHeading($genderForCreate === LegacyDogGender::Male ? __('Create Father') : __('Create Mother'))
                    ->modalWidth('6xl')
                    ->label($genderForCreate === LegacyDogGender::Male ? __('Create new dog as Father') : __('Create new dog as Mother'))
                    ->color('warning')
                    ->icon('fas-circle-plus');
            })
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
            )
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state) use ($field) {
                // Persist immediate update to the subject dog
                $this->subject->setAttribute($field, $state);
                $this->subject->save();

                // Refresh relations and local form state
                $this->subject->unsetRelations();
                $this->subject->load(['father', 'mother']);

                $this->form->fill([
                    'FatherSAGIR' => $this->subject->FatherSAGIR,
                    'MotherSAGIR' => $this->subject->MotherSAGIR,
                ]);
            })
            ->native(false)
            ->maxWidth('xl')
            ->columnSpan(2);
    }

    protected function optionLabel(PrevDog $d): string
    {
        $idPart = $d->sagir_prefix?->code() . "-" . $d->SagirID . " | " . ($d->ImportNumber ? $d->ImportNumber : __('w/o Imp'));
        $namePart = $d->full_name;
        $breed = $d->breed?->BreedName ?? null;
        $breed = $breed ? " • {$breed}" : '';

        return "{$idPart} • {$namePart}{$breed}";
    }

    protected function headingForDepth(PrevDog $subject_dog): string
    {
        $depth_heading = match ($this->depth) {
            1 => __('Parents'),
            2 => __('Grandparents'),
//            3 => __('Great Grandparent'),
            default => __('Generation :n', ['n' => $this->depth]),
        };
        $parent_of = __('Parents of');
        return "$depth_heading | $parent_of $subject_dog->SagirID";
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.parents-pair-form', [
            'subject' => $this->subject,
            'canRenderChildren' => $this->expanded && ($this->depth < $this->maxDepth),
        ]);
    }
}
