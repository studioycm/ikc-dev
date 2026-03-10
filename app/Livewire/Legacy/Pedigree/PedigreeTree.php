<?php

namespace App\Livewire\Legacy\Pedigree;

use App\Services\Legacy\Pedigree\PedigreeTreeBuilderService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PedigreeTree extends Component implements HasForms
{
    use InteractsWithForms;

    #[Locked]
    public int $dogId;

    public int $depth = 4;

    public string $direction = 'rtl';

    public string $density = 'comfortable';

    public bool $showPlaceholders = true;

    public ?array $settingsData = [];

    public function mount(int $dogId, int $depth = 4, ?string $direction = null): void
    {
        $this->dogId = $dogId;
        $this->depth = $this->sanitizeDepth($depth);
        $this->direction = $this->sanitizeDirection(
            $direction ?: $this->defaultDirection(),
        );

        $this->form->fill($this->defaultSettings());
        $this->applySettingsFromForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Certificate layout'))
                    ->description(__('Control the pedigree depth, direction, density, and placeholder behavior.'))
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Select::make('depth')
                                    ->label(__('Ancestor generations'))
                                    ->options([
                                        2 => '2',
                                        3 => '3',
                                        4 => '4',
                                        5 => '5',
                                        6 => '6',
                                        7 => '7',
                                        8 => '8',
                                    ])
                                    ->native(false)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 3,
                                    ]),

                                Select::make('direction')
                                    ->label(__('Direction'))
                                    ->options([
                                        'rtl' => 'RTL',
                                        'ltr' => 'LTR',
                                    ])
                                    ->native(false)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 3,
                                    ]),

                                Select::make('density')
                                    ->label(__('Density'))
                                    ->options([
                                        'comfortable' => __('Comfortable'),
                                        'compact' => __('Compact'),
                                    ])
                                    ->native(false)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 3,
                                    ]),

                                Toggle::make('show_placeholders')
                                    ->label(__('Show placeholder content'))
                                    ->helperText(__('When disabled, empty ancestors still keep their slot for alignment but render as minimal empty cards.'))
                                    ->live()
                                    ->columnSpan([
                                        'default' => 12,
                                        'md' => 3,
                                    ]),
                            ]),
                    ]),

                Section::make(__('Visible fields on ancestor nodes'))
                    ->description(__('All node fields are controlled from this main Livewire component.'))
                    ->schema([
                        Grid::make(12)
                            ->schema($this->visibleFieldComponents()),
                    ]),
            ])
            ->statePath('settingsData');
    }

    public function updated(string $name, mixed $value): void
    {
        if (str_starts_with($name, 'settingsData.')) {
            $this->applySettingsFromForm();
        }
    }

    public function applySettingsFromForm(): void
    {
        $state = $this->form->getState();

        $this->depth = $this->sanitizeDepth((int)($state['depth'] ?? $this->depth));
        $this->direction = $this->sanitizeDirection($state['direction'] ?? $this->direction);
        $this->density = $this->sanitizeDensity($state['density'] ?? $this->density);
        $this->showPlaceholders = (bool)($state['show_placeholders'] ?? true);

        $this->settingsData['visible_fields'] = $this->normalizeVisibleFields(
            $state['visible_fields'] ?? [],
        );

        unset($this->visibleNodeFields);
        unset($this->pedigree);
    }

    public function resetCertificateSettings(): void
    {
        $this->form->fill($this->defaultSettings());
        $this->applySettingsFromForm();
    }

    #[Computed]
    public function visibleNodeFields(): array
    {
        return $this->normalizeVisibleFields(
            $this->settingsData['visible_fields'] ?? [],
        );
    }

    #[Computed]
    public function pedigree(): array
    {
        return app(PedigreeTreeBuilderService::class)->build(
            dogId: $this->dogId,
            depth: $this->depth,
            direction: $this->direction,
            includeTitles: $this->visibleNodeFields['titles'] ?? false,
        );
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.pedigree-tree');
    }

    protected function visibleFieldComponents(): array
    {
        $components = [];

        foreach ($this->nodeFieldDefinitions() as $key => $config) {
            $components[] = Toggle::make("visible_fields.{$key}")
                ->label($config['label'])
                ->live()
                ->columnSpan([
                    'default' => 12,
                    'sm' => 6,
                    'xl' => 4,
                ]);
        }

        return $components;
    }

    protected function defaultSettings(): array
    {
        return [
            'depth' => $this->depth,
            'direction' => $this->direction,
            'density' => $this->density,
            'show_placeholders' => $this->showPlaceholders,
            'visible_fields' => $this->defaultVisibleFields(),
        ];
    }

    protected function defaultVisibleFields(): array
    {
        $defaults = [];

        foreach ($this->nodeFieldDefinitions() as $key => $config) {
            $defaults[$key] = (bool)($config['default'] ?? false);
        }

        return $defaults;
    }

    protected function normalizeVisibleFields(array $state): array
    {
        $normalized = [];

        foreach ($this->nodeFieldDefinitions() as $key => $config) {
            $normalized[$key] = (bool)($state[$key] ?? $config['default'] ?? false);
        }

        return $normalized;
    }

    protected function nodeFieldDefinitions(): array
    {
        return [
            'name_he' => [
                'label' => __('Hebrew Name'),
                'default' => true,
            ],
            'name_en' => [
                'label' => __('English Name'),
                'default' => true,
            ],
            'sagir_id' => [
                'label' => __('Sagir ID'),
                'default' => true,
            ],
            'import_number' => [
                'label' => __('Import Number'),
                'default' => false,
            ],
            'breeding_house' => [
                'label' => __('Kennel'),
                'default' => true,
            ],
            'breed_name' => [
                'label' => __('Breed'),
                'default' => false,
            ],
            'color_name' => [
                'label' => __('Color'),
                'default' => true,
            ],
            'birth_date' => [
                'label' => __('Birth Date'),
                'default' => true,
            ],
            'age' => [
                'label' => __('Age'),
                'default' => false,
            ],
            'titles' => [
                'label' => __('Titles'),
                'default' => false,
            ],
        ];
    }

    protected function sanitizeDepth(int $depth): int
    {
        return max(2, min(10, $depth));
    }

    protected function sanitizeDirection(?string $direction): string
    {
        return $direction === 'ltr' ? 'ltr' : 'rtl';
    }

    protected function sanitizeDensity(?string $density): string
    {
        return $density === 'compact' ? 'compact' : 'comfortable';
    }

    protected function defaultDirection(): string
    {
        return str_starts_with(app()->getLocale(), 'he') ? 'rtl' : 'ltr';
    }
}
