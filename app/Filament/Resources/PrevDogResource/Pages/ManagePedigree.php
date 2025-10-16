<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

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

    /** The selected main dog SagirID */
    public ?int $rootSagir = null;

    public ?array $data = [];

    public function mount(int|string|null $record = null): void
    {
        if ($record) {
            // When arriving from a record route, $record is the PK id
            $this->record = $this->resolveRecord($record);
            $this->rootSagir = $this->record->SagirID;
        } else {
            // Ensure InteractsWithRecord always has a Model instance to work with
            // so internal methods like getBreadcrumbs() don't fail.
            $this->record = PrevDog::make();
        }

        $this->form->fill([
            'rootSagir' => $this->rootSagir,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Select main dog'))
                    ->schema([
                        Grid::make(1)->schema([
                            Select::make('rootSagir')
                                ->label(__('Dog'))
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search) {
                                    return PrevDog::query()
                                        ->where(function ($q) use ($search) {
                                            $q->where('SagirID', 'like', "%{$search}%")
                                                ->orWhere('Eng_Name', 'like', "%{$search}%")
                                                ->orWhere('Heb_Name', 'like', "%{$search}%")
                                                ->orWhere('Chip', 'like', "%{$search}%")
                                                ->orWhere('ImportNumber', 'like', "%{$search}%");
                                        })
                                        ->limit(30)
                                        ->get()
                                        ->mapWithKeys(fn(PrevDog $d) => [
                                            $d->SagirID => $this->optionLabel($d),
                                        ])
                                        ->all();
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    if (!$value) {
                                        return null;
                                    }
                                    $d = PrevDog::query()->where('SagirID', $value)->with(['breed', 'color'])->first();

                                    return $d ? $this->optionLabel($d) : (string)$value;
                                })
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state) {
                                    $this->rootSagir = $state ?: null;
                                    $this->record = $state
                                        ? PrevDog::where('SagirID', $state)->first()
                                        : PrevDog::make();
                                })
                                ->native(false),
                        ]),
                    ])
                    ->maxWidth('md'),
            ])
            ->statePath('data');
    }

    protected function optionLabel(PrevDog $d): string
    {
        $idPart = $d->SagirID ? ($d->sagir_prefix?->code() . $d->SagirID) : ($d->ImportNumber ?: '—');
        $namePart = trim(($d->Eng_Name ?? '') . (($d->Eng_Name && $d->Heb_Name) ? ' / ' : '') . ($d->Heb_Name ?? '')) ?: '—';
        $breed = $d->breed?->BreedName ?? $d->breed?->name ?? null;
        $breed = $breed ? " • {$breed}" : '';
        $color = $d->color?->ColorNameHE ?? $d->color?->name ?? null;
        $color = $color ? " • {$color}" : '';

        return "{$idPart} • {$namePart}{$breed}{$color}";
    }

}
