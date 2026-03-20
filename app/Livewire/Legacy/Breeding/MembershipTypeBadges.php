<?php

namespace App\Livewire\Legacy\Breeding;

use App\Models\PrevDog;
use App\Services\Legacy\LegacyMembershipResolverService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class MembershipTypeBadges extends Component
{
    #[Reactive]
    public ?string $sagirId = null;

    #[Reactive]
    public ?int $prevUserId = null;

    public array $strategies = [];

    public string $title = '';

    protected LegacyMembershipResolverService $resolver;

    public function boot(LegacyMembershipResolverService $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function mount(
        ?string $sagirId = null,
        ?int    $prevUserId = null,
        array   $strategies = [],
        string  $title = '',
    ): void
    {
        $this->sagirId = $sagirId;
        $this->prevUserId = $prevUserId ?? auth()->user()?->prev_user_id;
        $this->strategies = $strategies;
        $this->title = $title;
    }

    #[Computed]
    public function badges(): array
    {
        if (!filled($this->sagirId)) {
            return [];
        }

        $dog = PrevDog::query()
            ->with(['owners', 'breed.clubs'])
            ->where('SagirID', $this->sagirId)
            ->first();

        if (!$dog) {
            return [];
        }

        return $this->resolver->eligibleMembershipTypes(
            dog: $dog,
            prevUserId: $this->prevUserId,
            strategies: $this->strategies !== [] ? $this->strategies : null,
        );
    }

    public function render()
    {
        return view('livewire.legacy.breeding.membership-type-badges');
    }
}
