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
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class PedigreeTree extends Component implements HasForms
{
    use InteractsWithForms;

    #[Locked]
    public int $dogId;

    public bool $showBuilder = true;

    public int $depth = 4;

    public string $direction = 'rtl';

    public string $density = 'comfortable';

    public string $fontScale = 'normal';

    public string $cardHeight = 'normal';

    public string $rootTitlesMode = 'compact';

    public bool $showPlaceholders = true;

    public array $visibleNodeFields = [];

    public ?array $settingsData = [];

    public ?array $pedigreeData = null;

    public ?string $loadError = null;

    public ?string $loadErrorTechnical = null;

    public bool $hasLoaded = false;

    public array $initialSettings = [];

    protected PedigreeTreeBuilderService $builderService;

    public function boot(PedigreeTreeBuilderService $builderService): void
    {
        $this->builderService = $builderService;
    }

    public function mount(
        int   $dogId,
        ?int  $depth = null,
        bool  $showBuilder = true,
        array $settings = [],
    ): void
    {
        $this->dogId = $dogId;
        $this->showBuilder = $showBuilder;
        $this->direction = $this->resolveDirectionFromLocale();

        $this->initialSettings = $this->resolveInitialSettings(
            depth: $depth,
            overrides: $settings,
        );

        $this->applyResolvedSettings($this->initialSettings);

        $this->form->fill($this->defaultSettings());
        $this->syncSettingsFromForm(false);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Pedigree Builder'))
                    ->description(__('Configure pedigree depth, density, typography, card height, and the main-title display mode'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(12)
                            ->schema([
                                Select::make('depth')
                                    ->label(__('Generations'))
                                    ->options(collect(range(2, 10))->mapWithKeys(fn(int $value): array => [$value => (string)$value])->all())
                                    ->native(false)
                                    ->required()
                                    ->helperText(__('Depths 7-10 are much heavier and may take longer to build'))
                                    ->columnSpan(['default' => 12, 'md' => 2]),

                                Select::make('density')
                                    ->label(__('Density'))
                                    ->options([
                                        'comfortable' => __('Comfortable'),
                                        'compact' => __('Compact'),
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 2]),

                                Select::make('font_scale')
                                    ->label(__('Font size'))
                                    ->options([
                                        'small' => __('Small font'),
                                        'normal' => __('Normal'),
                                        'large' => __('Large font'),
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 2]),

                                Select::make('card_height')
                                    ->label(__('Card height'))
                                    ->options([
                                        'short' => __('Short'),
                                        'normal' => __('Normal'),
                                        'tall' => __('Tall'),
                                        'x_tall' => __('Extra tall'),
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 2]),

                                Select::make('root_titles_mode')
                                    ->label(__('Main dog titles'))
                                    ->options([
                                        'compact' => __('Compact'),
                                        'expanded' => __('Expanded'),
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(['default' => 12, 'md' => 2]),

                                Toggle::make('show_placeholders')
                                    ->label(__('Show empty cards'))
                                    ->inline(false)
                                    ->columnSpan(['default' => 12, 'md' => 2]),
                            ]),
                    ]),

                Section::make(__('Visible fields'))
                    ->description(__('Control which fields appear on ancestor cards'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(12)
                            ->schema($this->visibleFieldComponents()),
                    ]),
            ])
            ->statePath('settingsData');
    }

    public function loadPedigree(): void
    {
        $this->direction = $this->resolveDirectionFromLocale();
        $this->loadError = null;
        $this->loadErrorTechnical = null;

        if (function_exists('set_time_limit')) {
            @set_time_limit(45);
        }

        try {
            $this->pedigreeData = $this->builderService->build(
                dogId: $this->dogId,
                depth: $this->depth,
                direction: $this->direction,
                includeNodeTitles: $this->visibleNodeFields['titles'] ?? false,
            );

            $this->hasLoaded = true;
        } catch (Throwable $throwable) {
            report($throwable);

            $message = mb_strtolower($throwable->getMessage());

            $this->loadError = str_contains($message, 'maximum execution time')
            || str_contains($message, 'timed out')
            || str_contains($message, 'timeout')
                ? __('The pedigree tree took too long to build. Try fewer generations, shorter cards, or hide ancestor titles and try again.')
                : __('The pedigree tree could not be built right now. Review the settings and try again.');

            $this->loadErrorTechnical = $throwable->getMessage();
            $this->hasLoaded = true;
        }
    }

    public function submitSettings(): void
    {
        $this->syncSettingsFromForm(true);
    }

    public function resetCertificateSettings(): void
    {
        $this->applyResolvedSettings($this->resolveInitialSettings(depth: null, overrides: $this->initialSettings));
        $this->form->fill($this->defaultSettings());
        $this->loadPedigree();
    }

    public function retryLoadPedigree(): void
    {
        $this->loadPedigree();
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.pedigree-tree');
    }

    public function treeRowHeight(): string
    {
        return match ($this->density) {
            'compact' => match ($this->cardHeight) {
                'short' => '8rem',
                'normal' => '9.5rem',
                'tall' => '11rem',
                'x_tall' => '12.5rem',
                default => '9.5rem',
            },
            default => match ($this->cardHeight) {
                'short' => '11rem',
                'normal' => '13rem',
                'tall' => '15rem',
                'x_tall' => '17rem',
                default => '13rem',
            },
        };
    }

    public function treeColumnMinWidth(): string
    {
        return $this->density === 'compact' ? '13.75rem' : '16rem';
    }

    protected function syncSettingsFromForm(bool $shouldLoad): void
    {
        $state = $this->form->getState();

        $this->depth = $this->sanitizeDepth((int)($state['depth'] ?? $this->depth));
        $this->density = $this->sanitizeDensity($state['density'] ?? $this->density);
        $this->fontScale = $this->sanitizeFontScale($state['font_scale'] ?? $this->fontScale);
        $this->cardHeight = $this->sanitizeCardHeight($state['card_height'] ?? $this->cardHeight);
        $this->rootTitlesMode = $this->sanitizeRootTitlesMode($state['root_titles_mode'] ?? $this->rootTitlesMode);
        $this->showPlaceholders = (bool)($state['show_placeholders'] ?? true);
        $this->visibleNodeFields = $this->normalizeVisibleFields($state['visible_fields'] ?? []);
        $this->direction = $this->resolveDirectionFromLocale();

        if ($shouldLoad) {
            $this->loadPedigree();
        }
    }

    protected function visibleFieldComponents(): array
    {
        $components = [];

        foreach ($this->nodeFieldDefinitions() as $key => $config) {
            $components[] = Toggle::make("visible_fields.{$key}")
                ->label($config['label'])
                ->columnSpan(['default' => 12, 'sm' => 3, 'xl' => 2]);
        }

        return $components;
    }

    protected function defaultSettings(): array
    {
        $settings = $this->initialSettings !== []
            ? $this->initialSettings
            : $this->resolveInitialSettings(depth: $this->depth, overrides: []);

        return $this->settingsFormState($settings);
    }

    protected function settingsFormState(array $settings): array
    {
        return [
            'depth' => $settings['depth'],
            'density' => $settings['density'],
            'font_scale' => $settings['font_scale'],
            'card_height' => $settings['card_height'],
            'root_titles_mode' => $settings['root_titles_mode'],
            'show_placeholders' => $settings['show_placeholders'],
            'visible_fields' => $settings['visible_fields'],
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

    protected function applyResolvedSettings(array $settings): void
    {
        $this->depth = $settings['depth'];
        $this->density = $settings['density'];
        $this->fontScale = $settings['font_scale'];
        $this->cardHeight = $settings['card_height'];
        $this->rootTitlesMode = $settings['root_titles_mode'];
        $this->showPlaceholders = $settings['show_placeholders'];
        $this->visibleNodeFields = $settings['visible_fields'];
    }

    protected function resolveInitialSettings(?int $depth, array $overrides): array
    {
        if ($depth !== null) {
            $overrides['depth'] = $depth;
        }

        $settings = array_replace_recursive(
            config('pedigree_tree.defaults', []),
            $overrides,
        );

        return [
            'depth' => $this->sanitizeDepth((int)($settings['depth'] ?? $this->depth)),
            'density' => $this->sanitizeDensity($settings['density'] ?? $this->density),
            'font_scale' => $this->sanitizeFontScale($settings['font_scale'] ?? $this->fontScale),
            'card_height' => $this->sanitizeCardHeight($settings['card_height'] ?? $this->cardHeight),
            'root_titles_mode' => $this->sanitizeRootTitlesMode($settings['root_titles_mode'] ?? $this->rootTitlesMode),
            'show_placeholders' => (bool)($settings['show_placeholders'] ?? $this->showPlaceholders),
            'visible_fields' => $this->normalizeVisibleFields($settings['visible_fields'] ?? []),
        ];
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
            'sagir_id' => ['label' => __('Sagir ID'), 'default' => true],
            'import_number' => ['label' => __('Import Number'), 'default' => false],
            'breeding_house' => ['label' => __('Kennel'), 'default' => true],
            'breed_name' => ['label' => __('Breed'), 'default' => false],
            'color_name' => ['label' => __('Color'), 'default' => true],
            'birth_date' => ['label' => __('Birth Date'), 'default' => true],
            'age' => ['label' => __('Age'), 'default' => false],
            'titles' => ['label' => __('Titles'), 'default' => false],
        ];
    }

    protected function sanitizeDepth(int $depth): int
    {
        return max(2, min(10, $depth));
    }

    protected function sanitizeDensity(?string $density): string
    {
        return $density === 'compact' ? 'compact' : 'comfortable';
    }

    protected function sanitizeFontScale(?string $fontScale): string
    {
        return in_array($fontScale, ['small', 'normal', 'large'], true) ? $fontScale : 'normal';
    }

    protected function sanitizeCardHeight(?string $cardHeight): string
    {
        return in_array($cardHeight, ['short', 'normal', 'tall', 'x_tall'], true) ? $cardHeight : 'normal';
    }

    protected function sanitizeRootTitlesMode(?string $mode): string
    {
        return in_array($mode, ['compact', 'expanded'], true) ? $mode : 'compact';
    }

    protected function resolveDirectionFromLocale(): string
    {
        $locale = App::currentLocale();

        return str_starts_with($locale, 'he') || str_starts_with($locale, 'ar')
            ? 'rtl'
            : 'ltr';
    }
}
