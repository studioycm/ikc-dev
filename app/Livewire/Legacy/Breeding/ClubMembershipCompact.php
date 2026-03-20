<?php

namespace App\Livewire\Legacy\Breeding;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ClubMembershipCompact extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Reactive]
    public ?array $membershipState = null;

    public function viewDetailsAction(): Action
    {
        return Action::make('viewDetails')
            ->label(__('More details'))
            ->icon('heroicon-m-information-circle')
            ->button()
            ->outlined()
            ->visible(fn(): bool => filled($this->membershipState))
            ->modalHeading(__('Club membership details'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'))
            ->modalWidth('lg')
            ->modalContent(fn(): View => view('livewire.legacy.breeding.club-membership-details-modal', [
                'membershipState' => $this->membershipState,
            ]));
    }

    public function render(): View
    {
        return view('livewire.legacy.breeding.club-membership-compact');
    }
}
