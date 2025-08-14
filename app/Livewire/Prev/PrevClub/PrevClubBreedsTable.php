<?php

namespace App\Livewire\Prev\PrevClub;

use App\Filament\Resources\PrevBreedResource;
use App\Models\PrevBreed;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class PrevClubBreedsTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Model $record;

    protected int $clubId;

    public function mount(Model $record): void
    {
        // Ensure we have a scalar id, fail fast if missing.
        $this->clubId = (int) ($record->id ?? 0);
    }

    public function table(Table $table): Table
    {
        return $table
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
            ->where('bc.club_id', $this->record->id)
            ->whereNull('bc.deleted_at')
            ->groupBy([
                'BreedsDB.id',
                'BreedsDB.BreedName',
                'BreedsDB.BreedNameEN',
                'BreedsDB.BreedCode',
            ]);
    }

    //    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    //    {
    //        $panel = Filament::getCurrentPanel();
    //
    //        if ($panel && method_exists($panel, 'makeTranslatableContentDriver')) {
    //            return $panel->makeTranslatableContentDriver();
    //        }
    //
    //        return null;
    //    }

    public function render(): View
    {
        return view('livewire.resources.prev-club.prev-club-breeds-table');

    }
}
