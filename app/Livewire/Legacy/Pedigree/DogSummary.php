<?php

namespace App\Livewire\Legacy\Pedigree;

use App\Models\PrevDog;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DogSummary extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public int $subjectId;

    public PrevDog $subject;

    public function mount(int $subjectId): void
    {
        $this->subjectId = $subjectId;
        $this->subject = PrevDog::with(['breed', 'color', 'owners', 'titles'])->findOrFail($subjectId);
    }

    public function summary(Infolist $infolist): Infolist
    {
        $d = $this->subject;

        return $infolist->record($d)->schema([
            Grid::make(8)->schema([
                TextEntry::make('full_name')
                    ->label('')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight('bold')
                    ->columnSpan(2),
                TextEntry::make('isbr')
                    ->label(__('I.S.B.R'))
                    ->state(fn() => $d->sagir_prefix?->code() . ' - ' . $d->SagirID . ' | ' . ($d->ImportNumber ?: '—')),
                TextEntry::make('birth')->label(__('D.O.B'))->state(fn() => optional($d->BirthDate)->format('Y-m-d') ?? '—'),
                TextEntry::make('breed')->label(__('Breed'))->state(fn() => $d->breed?->BreedName ?? $d->breed?->name ?? '—'),
                TextEntry::make('color')->label(__('Color'))->state(fn() => $d->color?->ColorNameHE ?? $d->color?->name ?? '—'),
                TextEntry::make('dna')->label(__('DNA'))->state(fn() => $d->DnaID ?: '—'),
                TextEntry::make('chip')->label(__('Chip'))->state(fn() => $d->Chip ?: '—'),
                TextEntry::make('owners')
                    ->label(__('Owners'))
                    ->state(fn() => $d->owners?->pluck('name')->implode(', ') ?: '—')
                    ->columnSpan(2),
                TextEntry::make('titles')
                    ->label(__('Titles'))
                    ->state(fn() => $d->titles?->pluck('name')->implode(', ') ?: '—')
                    ->columnSpan(2),
            ]),
        ]);
    }

    public function render(): View
    {
        return view('livewire.legacy.pedigree.dog-summary');
    }
}
