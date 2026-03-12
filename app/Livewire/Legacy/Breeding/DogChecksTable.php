<?php

namespace App\Livewire\Legacy\Breeding;

use App\Services\Legacy\Breeding\BreedingDogCheckService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class DogChecksTable extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Reactive]
    public ?string $sagirId = null;

    public string $role = 'female';

    public string $title = '';

    public array $configOverrides = [];

    public ?string $selectedActionKey = null;

    public ?string $selectedActionHeading = null;

    public ?string $selectedActionDescription = null;

    protected BreedingDogCheckService $service;

    public function boot(BreedingDogCheckService $service): void
    {
        $this->service = $service;
    }

    public function mount(
        ?string $sagirId = null,
        string  $role = 'female',
        string  $title = '',
        array   $configOverrides = [],
    ): void
    {
        $this->sagirId = $sagirId;
        $this->role = $role;
        $this->title = $title;
        $this->configOverrides = $configOverrides;
    }

    #[Computed]
    public function report(): ?array
    {
        return $this->service->buildBySagirId(
            sagirId: $this->sagirId,
            role: $this->role,
            overrides: $this->configOverrides,
        );
    }

    public function viewDetailsAction(string $checkKey): Action
    {
        return Action::make("viewDetails_{$checkKey}")
            ->icon('heroicon-m-information-circle')
            ->modalHeading(__('Check Details'))
            ->modalContent(function () use ($checkKey) {
                $report = $this->report;
                if (!$report) {
                    return null;
                }

                $check = collect($report['checks'])->firstWhere('key', $checkKey);
                if (!$check) {
                    return null;
                }

                return view('livewire.legacy.breeding.dog-check-details-modal', [
                    'check' => $check,
                    'dog' => $report['dog'],
                ]);
            })
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'));
    }

    public function configuredAction(string $actionKey, string $heading, string $description): Action
    {
        return Action::make("rowAction_{$actionKey}")
            ->modalHeading($heading)
            ->modalDescription($description)
            ->action(function () use ($actionKey, $heading, $description): void {
                $this->selectedActionKey = $actionKey;
                $this->selectedActionHeading = $heading;
                $this->selectedActionDescription = $description;
            });
    }

    public function render()
    {
        return view('livewire.legacy.breeding.dog-checks-table');
    }
}
