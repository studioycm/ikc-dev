<?php

namespace App\Livewire\Legacy\Pedigree;

use App\Models\PrevDog;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PedigreeTree extends Component
{
//    use InteractsWithActions;

    public int $dogId;

    public int $depth = 4;

    public bool $loaded = false;

    public ?PrevDog $dog = null;

    protected array $columns = [
        'id',
        'GenderID',
        'SagirID',
        'Heb_Name',
        'Eng_Name',
        'FatherSAGIR',
        'MotherSAGIR',
        'RaceID',
        'ColorID',
        'ImportNumber',
        'BirthDate',
    ];

    public function mount(int $dogId, int $depth = 3): void
    {
        $this->dogId = $dogId;
        $this->depth = $depth;
        $this->load();
    }

    /*
    |--------------------------------------------------------------------------
    | Filament Action: Load Pedigree
    |--------------------------------------------------------------------------
    */

//    public function loadPedigreeAction(): Action
//    {
//        return Action::make('loadPedigree')
//            ->label(__('Load Pedigree'))
//            ->icon('heroicon-o-arrow-path')
//            ->color('primary')
//            ->action(fn () => $this->load());
//    }

    protected function load(): void
    {
        $this->dog = PrevDog::query()
            ->select($this->columns)
            ->withPedigree($this->depth, $this->columns)
            ->withBreedName()
            ->with([
                'color',
                'titles' => fn($q) => $q->limit(11),
            ])
            ->find($this->dogId);

        $this->loaded = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Computed Generations (Grid Layout)
    |--------------------------------------------------------------------------
    */

    public function getGenerationsProperty(): array
    {
        if (!$this->dog) {
            return [];
        }

        $generations = [];
        $current = [$this->dog];

        for ($i = 0; $i <= $this->depth; $i++) {
            $generations[$i] = $current;

            $next = [];

            foreach ($current as $dog) {
                $next[] = $dog->father;
                $next[] = $dog->mother;
            }

            $current = array_filter($next);
        }

        return $generations;
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.pedigree-tree');
    }
}
