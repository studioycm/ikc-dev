<?php

namespace App\Filament\Resources\PrevDogResource\RelationManagers;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\Resources\PrevDogResource;
use App\Models\PrevDog;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'childrenAsFather';

    public function getRelationship(): Relation|Builder
    {
        $parent = $this->getOwnerRecord();

        return $parent->GenderID === LegacyDogGender::Male ?
            $parent->childrenAsFather() :
            $parent->childrenAsMother();
    }

    protected static ?string $recordTitleAttribute = 'SagirID';

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
            ->contentGrid([
                'md' => 2,
                'lg' => 6,
            ])
            ->defaultSort('BirthDate', 'desc')
            ->defaultGroup('partner')
            ->groups([
                Group::make('partner')
                    ->label(fn(): string => $this->parentGenderMale() ? __('Dam') : __('Sire'))
                    ->getTitleFromRecordUsing(fn(PrevDog $record) => $this->parentGenderMale() ? "{$record->mother->full_name} ({$record->mother->SagirID})" : "{$record->father->full_name} ({$record->father->SagirID})")
                    ->getDescriptionFromRecordUsing(fn(PrevDog $record) => $record->BirthDate->format('Y-m-d'))
                    ->collapsible()
                    ->column($this->parentColumn()),
                Group::make('birth_date')
                    ->label(__('Birth Date'))
                    ->getTitleFromRecordUsing(fn(PrevDog $record) => $record->BirthDate->format('Y-m-d'))
                    ->getDescriptionFromRecordUsing(fn(PrevDog $record) => $this->parentGenderMale() ? "{$record->mother->full_name} ({$record->mother->SagirID})" : "{$record->father->full_name} ({$record->father->SagirID})")
                    ->collapsible()
                    ->column('BirthDate'),
            ])
            ->columns([
                Stack::make([
                    TextColumn::make('SagirID')
                        ->label(__('Sagir'))
                        ->numeric(decimalPlaces: 0, thousandsSeparator: '')
                        ->searchable(),
                    TextColumn::make('full_name')
                        ->label(__('Name'))
                        ->searchable(['Heb_Name', 'Eng_Name'])
                        ->wrap(),
//                    TextColumn::make('parent_role')
//                        ->label(__('Parent'))
//                        ->state(function (PrevDog $record): string {
//                            $ownerSagirId = $this->getOwnerRecord()->SagirID;
//
//                            return $record->FatherSAGIR === $ownerSagirId ? __('Father') : __('Mother');
//                        })
//                        ->badge()
//                        ->toggleable(),
//                    TextColumn::make('parenthood_partner')
//                        ->label(__('Partner'))
//                        ->state(function (PrevDog $record): ?string {
//                            $ownerSagirId = $this->getOwnerRecord()->SagirID;
//
//                            return $record->FatherSAGIR === $ownerSagirId
//                                ? $record->mother?->full_name
//                                : $record->father?->full_name;
//                        })
//                        ->toggleable(),
                    TextColumn::make('BirthDate')
                        ->label(__('Birth Date'))
                        ->date()
                        ->toggleable(),
                ])->space(3),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalWidth('7xl'),
            ])
            ->bulkActions([]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return PrevDogResource::infolist($infolist);
    }

    protected function parentGenderMale(): bool
    {
        return $this->getOwnerRecord()->GenderID === LegacyDogGender::Male;
    }

    protected function parentColumn(): string
    {
        $parent = $this->getOwnerRecord();

        return $parent->GenderID === LegacyDogGender::Male ? "MotherSAGIR" : "FatherSAGIR";
    }
}
