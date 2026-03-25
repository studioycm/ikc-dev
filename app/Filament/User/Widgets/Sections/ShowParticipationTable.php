<?php

namespace App\Filament\User\Widgets\Sections;

use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevShowDog;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ShowParticipationTable extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $dogSagirIds = $this->getCurrentPrevUserDogSagirIds();

        return $table
            ->query(
                PrevShowDog::query()
                    ->with([
                        'show:id,TitleName,StartDate,EndDate,ClubID',
                        'show.club:id,Name,EngName',
                        'dog:id,SagirID,Heb_Name,Eng_Name',
                        'arena:id,GroupName',
                        'showClass:id,ShowID,DataID,ClassName',
                        'breed:id,BreedCode,BreedName,BreedNameEN',
                        'prevShowResult',
                    ])
                    ->when(
                        $dogSagirIds === [],
                        fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                        fn(Builder $query): Builder => $query->whereIn('SagirID', $dogSagirIds),
                    )
                    ->orderByDesc('ShowID')
            )
            ->columns([
                Tables\Columns\TextColumn::make('show.TitleName')
                    ->label(__('Show'))
                    ->description(fn(PrevShowDog $record): ?string => $record->show?->club?->Name)
                    ->searchable(['ShowsDB.TitleName'])
                    ->sortable(['ShowID']),
                Tables\Columns\TextColumn::make('dog.full_name')
                    ->label(__('Dog'))
                    ->description(fn(PrevShowDog $record): ?string => $record->dog?->SagirID ? __('Sagir') . ': ' . $record->dog->SagirID : null)
                    ->searchable(['DogsDB.Heb_Name', 'DogsDB.Eng_Name', 'Shows_Dogs_DB.SagirID'])
                    ->sortable(['SagirID']),
                Tables\Columns\TextColumn::make('show.StartDate')
                    ->label(__('Show Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('showClass.ClassName')
                    ->label(__('Class'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('arena.GroupName')
                    ->label(__('Arena'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->description(fn(PrevShowDog $record): ?string => $record->breed?->BreedNameEN)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('OrderID')
                    ->label(__('Order'))
                    ->badge()
                    ->color('primary')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('prevShowResult.DataID')
                    ->label(__('Result'))
                    ->placeholder(__('Pending'))
                    ->color('success')
                    ->icon('heroicon-o-trophy')
                    ->iconPosition(IconPosition::After)
                    ->description(fn(PrevShowDog $record): string => $record->prevShowResult?->results_labels ? implode(', ', $record->prevShowResult->results_labels) : __('No published result'))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('ShowID')
                    ->label(__('Show'))
                    ->relationship('show', 'TitleName')
                    ->searchable()
                    ->preload(false),
                SelectFilter::make('BreedID')
                    ->label(__('Breed'))
                    ->relationship('breed', 'BreedName')
                    ->searchable(['BreedName', 'BreedNameEN'])
                    ->preload(false),
                SelectFilter::make('has_results')
                    ->label(__('Results'))
                    ->options([
                        'yes' => __('Has results'),
                        'no' => __('Pending results'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->has('prevShowResult'),
                            'no' => $query->doesntHave('prevShowResult'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn(PrevShowDog $record): string => __('Show Entry #:id', ['id' => $record->id]))
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist->schema([
                        Section::make(__('Show Entry'))
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('show.TitleName')->label(__('Show')),
                                        TextEntry::make('show.club.Name')->label(__('Club')),
                                        TextEntry::make('show.StartDate')->label(__('Show Date'))->date(),
                                        TextEntry::make('dog.full_name')->label(__('Dog')),
                                        TextEntry::make('showClass.ClassName')->label(__('Class')),
                                        TextEntry::make('arena.GroupName')->label(__('Arena')),
                                        TextEntry::make('breed.BreedName')->label(__('Breed')),
                                        TextEntry::make('OrderID')->label(__('Order')),
                                        TextEntry::make('prevShowResult.DataID')->label(__('Result ID')),
                                    ]),
                            ]),
                        Section::make(__('Published Result'))
                            ->visible(fn(PrevShowDog $record): bool => $record->prevShowResult !== null)
                            ->schema([
                                TextEntry::make('prevShowResult.results_labels')
                                    ->label(__('Awards'))
                                    ->badge(),
                                TextEntry::make('prevShowResult.titles_labels')
                                    ->label(__('Titles'))
                                    ->badge(),
                            ]),
                    ])),
                Action::make('viewResult')
                    ->label(__('View Result'))
                    ->icon('heroicon-o-trophy')
                    ->visible(fn(PrevShowDog $record): bool => $record->prevShowResult !== null)
                    ->modalHeading(__('Published Result'))
                    ->modalSubmitAction(false)
                    ->infolist(fn(Infolist $infolist): Infolist => $infolist->schema([
                        Section::make(__('Result Summary'))
                            ->schema([
                                TextEntry::make('prevShowResult.results_labels')
                                    ->label(__('Awards'))
                                    ->badge(),
                                TextEntry::make('prevShowResult.titles_labels')
                                    ->label(__('Titles'))
                                    ->badge(),
                            ]),
                    ])),
            ])
            ->heading(__('Shows & Results'))
            ->description(__('See upcoming entries, past participation, and published results for your dogs.'))
            ->defaultSort('ShowID', 'desc')
            ->paginated([5, 10, 25, 'all'])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No show participation found'))
            ->emptyStateDescription(__('Show registrations for your dogs will appear here.'))
            ->emptyStateIcon('heroicon-o-trophy');
    }
}
