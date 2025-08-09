<?php

namespace App\Livewire\Prev\PrevClub;

use App\Filament\Resources\PrevBreedResource;
use App\Models\PrevBreed;
use App\Models\PrevClub;
use Filament\Facades\Filament;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class PrevClubBreedsTable extends Component implements HasTable
{
    use InteractsWithTable;

    public int $clubId;

    public function mount(?int $clubId = null): void
    {
        // Ensure we have a scalar id, fail fast if missing.
        $this->clubId = (int) ($clubId ?? 0);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Breeds'))
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('BreedName')
                    ->label(__('Breed'))
                    ->description(fn (PrevBreed $record): string => (string) ($record->BreedNameEN ?? ''))
                    ->url(fn (PrevBreed $record): string => PrevBreedResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('BreedCode')
                    ->label(__('Breed Code'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('dogs_count')
                    ->label(__('Dogs Count'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
            ])
            ->defaultSort('BreedName')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(25)
            ->striped()
            ->emptyStateHeading(__('No breeds found'));
    }

    protected function getTableQuery(): Builder
    {
        return PrevBreed::query()
            ->select([
                'BreedsDB.id',
                'BreedsDB.BreedName',
                'BreedsDB.BreedNameEN',
                'BreedsDB.BreedCode',
            ])
            ->selectRaw('COUNT(DISTINCT d.SagirID) AS dogs_count')
            ->join('breed_club as bc', 'bc.breed_id', '=', 'BreedsDB.id')
            ->leftJoin('DogsDB as d', function ($join) {
                $join->on('d.RaceID', '=', 'BreedsDB.BreedCode')
                     ->whereNull('d.deleted_at'); // keep LEFT JOIN behavior
            })
            ->where('bc.club_id', $this->clubId)
            ->whereNull('bc.deleted_at')
            ->groupBy([
                'BreedsDB.id',
                'BreedsDB.BreedName',
                'BreedsDB.BreedNameEN',
                'BreedsDB.BreedCode',
            ]);
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return Filament::getCurrentPanel()?->makeTranslatableContentDriver();
    }

    public function render(): View
    {
        return view('filament.resources.prev-club.prev-club-breeds-table');
    }
}
