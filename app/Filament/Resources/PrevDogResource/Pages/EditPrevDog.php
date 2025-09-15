<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevDog extends EditRecord
{
    protected static string $resource = PrevDogResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getHeading(): string|Htmlable
    {
        $dog = $this->getRecord();
        return __('Edit dog:') . ' ' . $dog->full_name . ' #' . $dog->SagirID;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
