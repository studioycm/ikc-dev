<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'childrenAsFather';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Children');
    }

    protected function getTableQuery(): Builder
    {
        $ownerSagirId = $this->getOwnerRecord()->SagirID;

        return PrevDog::query()
            ->where(function (Builder $query) use ($ownerSagirId): void {
                $query->where('FatherSAGIR', $ownerSagirId)
                    ->orWhere('MotherSAGIR', $ownerSagirId);
            })
            ->with(['father', 'mother']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('BirthDate', 'desc')
            ->columns([
                TextColumn::make('SagirID')
                    ->label(__('Sagir'))
                    ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('Child'))
                    ->searchable(['Heb_Name', 'Eng_Name'])
                    ->wrap(),
                TextColumn::make('parent_role')
                    ->label(__('Parent Role'))
                    ->state(function (PrevDog $record): string {
                        $ownerSagirId = $this->getOwnerRecord()->SagirID;

                        return $record->FatherSAGIR === $ownerSagirId ? __('Father') : __('Mother');
                    })
                    ->badge(),
                TextColumn::make('parenthood_partner')
                    ->label(__('Parenthood Partner'))
                    ->state(function (PrevDog $record): ?string {
                        $ownerSagirId = $this->getOwnerRecord()->SagirID;

                        return $record->FatherSAGIR === $ownerSagirId
                            ? $record->mother?->full_name
                            : $record->father?->full_name;
                    })
                    ->toggleable(),
                TextColumn::make('BirthDate')
                    ->label(__('Birth Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('breed.BreedName')
                    ->label(__('Breed'))
                    ->toggleable(),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View Child'))
                    ->url(fn(PrevDog $record): string => PrevDogResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
