<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevDog extends ViewRecord
{
    protected static string $resource = PrevDogResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . " " . __('dog/model/general.labels.singular') . ': ' . $this->record->full_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('pedigree')
                ->label(__('Manage Pedigree'))
                ->icon('heroicon-m-share')
                ->url(PrevDogResource::getUrl('pedigree', ['record' => $this->record])),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('dog/model/general.labels.singular');
    }
}
