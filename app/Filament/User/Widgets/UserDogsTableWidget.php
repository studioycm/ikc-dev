<?php

namespace App\Filament\User\Widgets;

use App\Enums\Legacy\LegacyDogGender;
use App\Models\PrevDog;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UserDogsTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected ?int $totalDogsCount = null;

    protected function getTotalDogsCount(): int
    {
        if ($this->totalDogsCount === null) {
            $this->totalDogsCount = PrevDog::query()
                ->whereHas('owners', function (Builder $query) {
                    $query->where('users.id', auth()->user()?->prevUser?->id);
                })
                ->count();
        }

        return $this->totalDogsCount;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PrevDog::query()
                    ->whereHas('owners', function (Builder $query) {
                        $query->where('users.id', auth()->user()?->prevUser?->id);
                    })
                    ->with([
                        'breed:BreedCode,BreedName,BreedNameEN',
                        'color:OldCode,ColorNameHE,ColorNameEN',
                        'hair:OldCode,HairNameHE,HairNameEN',
                        'father:id,SagirID,Heb_Name,Eng_Name',
                        'mother:id,SagirID,Heb_Name,Eng_Name',
                        'breedinghouse:GidulCode,HebName,EngName',
                        'owners:id,first_name,last_name,first_name_en,last_name_en,mobile_phone,email',
                        'titles:TitleCode,TitleName',
                    ])
                    ->orderBy('SagirID', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->description(fn(PrevDog $record): string => $record->id ? "ID: {$record->id}" : '')
                    ->size('lg')
                    ->weight(FontWeight::Bold)
                    ->color(fn(PrevDog $record): string => $record->sagir_prefix?->getColor() ?? 'gray')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Dog name'))
                    ->description(fn(PrevDog $record): string => $record->breed?->BreedName ?? '~')
                    ->searchable(['Heb_Name', 'Eng_Name'])
                    ->sortable(['Heb_Name']),
                Tables\Columns\TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date('Y-m-d')
                    ->description(fn(PrevDog $record): string => $record->age_years ?? '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('GenderID')
                    ->label(__('Gender'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('titles.name')
                    ->label(__('Titles'))
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
                Tables\Columns\TextColumn::make('father.full_name')
                    ->label(__('Father'))
                    ->description(fn(PrevDog $record): string => $record->father?->SagirID ?? 'n/a')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mother.full_name')
                    ->label(__('Mother'))
                    ->description(fn(PrevDog $record): string => $record->mother?->SagirID ?? 'n/a')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('breedinghouse.name')
                    ->label(__('Kennel'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('owners.full_name')
                    ->label(__('Other Owners'))
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->formatStateUsing(function (PrevDog $record, $state) {
                        // Exclude current user from the list
                        $currentUserId = auth()->user()?->prevUser?->id;
                        return $record->owners
                            ->where('id', '!=', $currentUserId)
                            ->pluck('full_name')
                            ->take(1)
                            ->join(', ');
                    })
                    ->toggleable(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::BelowContent)
            ->persistFiltersInSession(true)
            ->filtersFormColumns(4)
            ->deselectAllRecordsWhenFiltered(true)
            ->filters([
                Filter::make('GenderID')
                    ->label(__('Gender'))
                    ->form([
                        \Filament\Forms\Components\ToggleButtons::make('GenderID')
                            ->label(__('Gender'))
                            ->options(LegacyDogGender::class)
                            ->grouped()
                            ->nullable(),
                    ])
                    ->query(fn(Builder $query, array $data): Builder => $query->when(
                        filled($data['GenderID'] ?? null),
                        fn(Builder $q): Builder => $q->where('GenderID', $data['GenderID'])
                    )),
                Tables\Filters\SelectFilter::make('breed')
                    ->label(__('Breed'))
                    ->relationship('breed', 'BreedName')
                    ->multiple()
                    ->searchable(),
                Filter::make('BirthDate')
                    ->form([
                        Section::make(__('Birth Date Range'))
                            ->description(__('Leave "End" empty to use today'))
                            ->schema([
                                DatePicker::make('birth_date_start')
                                    ->label(__('Start'))
                                    ->native(false),
                                DatePicker::make('birth_date_end')
                                    ->label(__('End'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['birth_date_start'] ?? null,
                                fn(Builder $q, $date): Builder => $q->whereDate('BirthDate', '>=', $date)
                            )
                            ->when(
                                ($data['birth_date_start'] ?? null) && !($data['birth_date_end'] ?? null),
                                fn(Builder $q): Builder => $q->whereDate('BirthDate', '<=', now()->toDateString())
                            )
                            ->when(
                                $data['birth_date_end'] ?? null,
                                fn(Builder $q, $date): Builder => $q->whereDate('BirthDate', '<=', $date)
                            );
                    }),
                Filter::make('age_groups')
                    ->form([
                        \Filament\Forms\Components\ToggleButtons::make('age_ranges')
                            ->label(__('Age Groups'))
                            ->options([
                                'all' => __('All'),
                                'below_9m' => __('Below 9m'),
                                '9m_18m' => __('9-18 month'),
                                '18m_36m' => __('18-36 month'),
                                '3y_7y' => __('3-7 years'),
                                'above_7y' => __('Above 7y'),
                            ])
                            ->multiple()
                            ->columns(3)
                            ->gridDirection('row')
                            ->nullable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['age_ranges'])) {
                            return $query;
                        }

                        return $query->where(function (Builder $q) use ($data) {
                            foreach ($data['age_ranges'] as $range) {
                                $q->orWhere(function (Builder $subQ) use ($range) {
                                    $now = Carbon::now();
                                    match ($range) {
                                        'below_9m' => $subQ->where('BirthDate', '>', $now->copy()->subMonths(9)),
                                        '9m_18m' => $subQ->whereBetween('BirthDate', [
                                            $now->copy()->subMonths(18),
                                            $now->copy()->subMonths(9)
                                        ]),
                                        '18m_36m' => $subQ->whereBetween('BirthDate', [
                                            $now->copy()->subMonths(36),
                                            $now->copy()->subMonths(18)
                                        ]),
                                        '3y_7y' => $subQ->whereBetween('BirthDate', [
                                            $now->copy()->subYears(7),
                                            $now->copy()->subYears(3)
                                        ]),
                                        'above_7y' => $subQ->where('BirthDate', '<', $now->copy()->subYears(7)),
                                        default => null,
                                    };
                                });
                            }
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn(PrevDog $record): string => $record->full_name)
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist
                        ->schema([
                            Tabs::make(__('Dog Details'))->tabs([
                                Tab::make(__('Basic Info'))
                                    ->schema([
                                        InfolistGrid::make(2)->schema([
                                            TextEntry::make('SagirID')
                                                ->label(__('Sagir'))
                                                ->prefix(fn(PrevDog $record): string => $record->sagir_prefix?->code() ?? ''),
                                            TextEntry::make('full_name')
                                                ->label(__('Full Name')),
                                            TextEntry::make('BirthDate')
                                                ->label(__('Birth Date'))
                                                ->date('Y-m-d'),
                                            TextEntry::make('age_years')
                                                ->label(__('Age')),
                                            TextEntry::make('GenderID')
                                                ->label(__('Gender'))
                                                ->badge(),
                                            TextEntry::make('breed.BreedName')
                                                ->label(__('Breed')),
                                            TextEntry::make('color.ColorNameHE')
                                                ->label(__('Color')),
                                            TextEntry::make('Chip')
                                                ->label(__('Chip')),
                                            TextEntry::make('breedinghouse.name')
                                                ->label(__('Breeding House')),
                                        ]),
                                    ]),
                                Tab::make(__('Pedigree'))
                                    ->schema([
                                        TextEntry::make('no_pedigree')
                                            ->label(__('No Pedigree'))
                                            ->visible(fn(PrevDog $record): bool => empty($record->father) && empty($record->mother)),
                                        InfolistSection::make(__('Parents'))->schema([
                                            TextEntry::make('father.full_name')
                                                ->label(__('Father')),
                                            TextEntry::make('father.SagirID')
                                                ->label(__('Father ID')),
                                            TextEntry::make('mother.full_name')
                                                ->label(__('Mother')),
                                            TextEntry::make('mother.SagirID')
                                                ->label(__('Mother ID')),
                                        ])->columns(2)
                                            ->hidden(fn(PrevDog $record): bool => empty($record->father) && empty($record->mother)),
                                    ]),
                                Tab::make(__('Ownerships'))
                                    ->schema([
                                        RepeatableEntry::make('owners')
                                            ->label(__('All Owners'))
                                            ->schema([
                                                TextEntry::make('full_name')
                                                    ->label(__('Name')),
                                                TextEntry::make('mobile_phone')
                                                    ->label(__('Phone')),
                                                TextEntry::make('email')
                                                    ->label(__('Email')),
                                            ])
                                            ->grid(3),
                                    ]),
                                Tab::make(__('Titles'))
                                    ->schema([
                                        RepeatableEntry::make('titles')
                                            ->label(__('Titles'))
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('Title'))
                                                    ->weight(FontWeight::Bold),
                                                TextEntry::make('awarding.EventPlace')
                                                    ->label(__('Place')),
                                                TextEntry::make('awarding.EventDate')
                                                    ->label(__('Date'))
                                                    ->date('Y-m-d'),
                                            ])
                                            ->grid(3),
                                    ]),
                            ])->columnSpanFull(),
                        ])
                    ),
                Tables\Actions\Action::make('breeding')
                    ->label(__('Litter'))
                    ->tooltip('פתיחת תיק המלטה')
                    ->icon('heroicon-o-heart')
                    ->color('success')
                    ->modalHeading(__('Open Litter Report'))
                    ->modalContent(fn() => view('filament.user.modals.placeholder', ['message' => 'Breeding info coming soon']))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close')),
                Tables\Actions\Action::make('medical')
                    ->hiddenLabel()
                    ->tooltip(__('Medical'))
                    ->icon('heroicon-o-document')
                    ->color('danger')
                    ->modalHeading(__('Medical Records'))
                    ->modalContent(fn() => view('filament.user.modals.placeholder', ['message' => 'Medical records coming soon']))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close')),
                Tables\Actions\Action::make('shows')
                    ->hiddenLabel()
                    ->tooltip(__('Shows'))
                    ->icon('heroicon-o-trophy')
                    ->color('warning')
                    ->modalHeading(__('Show History'))
                    ->modalContent(fn() => view('filament.user.modals.placeholder', ['message' => 'Show history coming soon']))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close')),
            ])
            ->paginated([5, 10, 15, 20, 'all'])
            ->defaultPaginationPageOption(10)
            ->heading(fn() => __('My Dogs') . " ({$this->getTotalDogsCount()})")
            ->description(function () {
                $total = $this->getTotalDogsCount();
                $filtered = $this->getTable()->getRecords()->count();

                if ($total === $filtered) {
                    return __('Showing all :count :items', [
                        'count' => $total,
                        'items' => trans_choice('{1} dog|[2,*] dogs', $total),
                    ]);
                }

                return __('Showing :filtered of :total :items', [
                    'filtered' => $filtered,
                    'total' => $total,
                    'items' => trans_choice('{1} dog|[2,*] dogs', $total),
                ]);
            });
    }
}
