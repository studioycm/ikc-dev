<?php

namespace App\Filament\User\Widgets\Sections;

use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevBreeding;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class BreedingActivityTable extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $dogSagirIds = $this->getCurrentPrevUserDogSagirIds();
        $breedingHouseIds = $this->getCurrentPrevUserBreedingHouseIds();

        return $table
            ->query(
                PrevBreeding::query()
                    ->with([
                        'female:id,SagirID,Heb_Name,Eng_Name,BreedID',
                        'female.breed:id,BreedCode,BreedName,BreedNameEN',
                        'male:id,SagirID,Heb_Name,Eng_Name,BreedID',
                        'male.breed:id,BreedCode,BreedName,BreedNameEN',
                        'breedinghouse:id,HebName,EngName',
                        'createdBy:id,first_name,last_name,first_name_en,last_name_en,mobile_phone,email',
                        'puppies',
                    ])
                    ->when(
                        $dogSagirIds === [] && $breedingHouseIds === [],
                        fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                        function (Builder $query) use ($breedingHouseIds, $dogSagirIds): Builder {
                            return $query->where(function (Builder $breedingQuery) use ($breedingHouseIds, $dogSagirIds): void {
                                if ($dogSagirIds !== []) {
                                    $breedingQuery->whereIn('SagirId', $dogSagirIds)
                                        ->orWhereIn('MaleSagirId', $dogSagirIds);
                                }

                                if ($breedingHouseIds !== []) {
                                    $method = $dogSagirIds !== [] ? 'orWhereIn' : 'whereIn';
                                    $breedingQuery->{$method}('breeding_house_id', $breedingHouseIds);
                                }
                            });
                        },
                    )
                    ->orderByDesc('BreddingDate')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('female.full_name')
                    ->label(__('Female'))
                    ->description(fn(PrevBreeding $record): ?string => $record->female?->SagirID ? __('Sagir') . ': ' . $record->female->SagirID : null)
                    ->searchable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name'], isIndividual: true, isGlobal: false)
                    ->sortable(['SagirId']),
                Tables\Columns\TextColumn::make('male.full_name')
                    ->label(__('Male'))
                    ->description(fn(PrevBreeding $record): ?string => $record->male?->SagirID ? __('Sagir') . ': ' . $record->male->SagirID : null)
                    ->searchable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name'])
                    ->sortable(['MaleSagirId']),
                Tables\Columns\TextColumn::make('BreddingDate')
                    ->label(__('Breeding Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthing_date')
                    ->label(__('Birth Date'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('breedinghouse.name')
                    ->label(__('Kennel'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_payment')
                    ->label(__('Total Payment'))
                    ->money('ILS')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('puppies_count')
                    ->label(__('Puppies'))
                    ->counts('puppies')
                    ->sortable(),
                Tables\Columns\IconColumn::make('Male_DNA')
                    ->label(__('Male DNA'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('Female_DNA')
                    ->label(__('Female DNA'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(fn(): array => PrevBreeding::query()
                        ->whereNotNull('status')
                        ->distinct()
                        ->orderBy('status')
                        ->pluck('status', 'status')
                        ->all()),
                SelectFilter::make('payment_status')
                    ->label(__('Payment Status'))
                    ->options(fn(): array => PrevBreeding::query()
                        ->whereNotNull('payment_status')
                        ->distinct()
                        ->orderBy('payment_status')
                        ->pluck('payment_status', 'payment_status')
                        ->all()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn(PrevBreeding $record): string => __('Breeding #:id', ['id' => $record->id]))
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist->schema([
                        Section::make(__('Breeding Summary'))
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('female.full_name')->label(__('Female')),
                                        TextEntry::make('male.full_name')->label(__('Male')),
                                        TextEntry::make('breedinghouse.name')->label(__('Kennel')),
                                        TextEntry::make('BreddingDate')->label(__('Breeding Date'))->date(),
                                        TextEntry::make('birthing_date')->label(__('Birth Date'))->date(),
                                        TextEntry::make('createdBy.name')->label(__('Created By')),
                                        TextEntry::make('status')->label(__('Status'))->badge(),
                                        TextEntry::make('payment_status')->label(__('Payment Status'))->badge(),
                                        TextEntry::make('total_payment')->label(__('Total Payment'))->money('ILS'),
                                    ]),
                            ]),
                        Section::make(__('Checks & Flags'))
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('Rules_IsOwner')->label(__('Ownership Rule'))->formatStateUsing(fn(?bool $state): string => $state ? __('Passed') : __('Missing')),
                                        TextEntry::make('BreedMismatch')->label(__('Breed mismatch'))->formatStateUsing(fn(?bool $state): string => $state ? __('Yes') : __('No')),
                                        TextEntry::make('Male_DNA')->label(__('Male DNA'))->formatStateUsing(fn(?bool $state): string => $state ? __('Yes') : __('No')),
                                        TextEntry::make('Female_DNA')->label(__('Female DNA'))->formatStateUsing(fn(?bool $state): string => $state ? __('Yes') : __('No')),
                                    ]),
                            ]),
                        Section::make(__('Puppies'))
                            ->schema([
                                RepeatableEntry::make('puppies')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('temparory_name')->label(__('Temporary Name')),
                                        TextEntry::make('sagir_id')->label(__('Sagir')),
                                        TextEntry::make('gender')->label(__('Gender')),
                                        TextEntry::make('approval_status')->label(__('Approval Status')),
                                        TextEntry::make('is_dead')->label(__('Is Dead'))->formatStateUsing(fn(?bool $state): string => $state ? __('Yes') : __('No')),
                                    ])
                                    ->columns(5),
                            ])
                            ->heading(__('Puppies'))
                            ->collapsed(false),
                    ])),
            ])
            ->heading(__('Breedings'))
            ->description(__('Review your recorded breedings, payments, and puppy registrations.'))
            ->defaultSort('BreddingDate', 'desc')
            ->paginated([5, 10, 25, 'all'])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No breeding activity found'))
            ->emptyStateDescription(__('Breedings linked to your dogs or kennels will appear here.'))
            ->emptyStateIcon('heroicon-o-heart');
    }
}
