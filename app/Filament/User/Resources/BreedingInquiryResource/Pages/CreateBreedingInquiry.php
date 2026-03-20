<?php

namespace App\Filament\User\Resources\BreedingInquiryResource\Pages;

use App\Filament\User\Resources\BreedingInquiryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBreedingInquiry extends CreateRecord
{
    protected static string $resource = BreedingInquiryResource::class;

    public function mount(): void
    {
        parent::mount();

        if ($female = request()->get('female_sagir_id')) {
            $this->form->fill([
                'female_sagir_id' => $female,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['prev_user_id'] = $user->prev_user_id;

        $data['puppies'] = collect($data['puppies'] ?? [])
            ->map(function ($puppy) {
                $puppy['uuid'] = $puppy['uuid'] ?? (string)Str::uuid();
                return $puppy;
            })
            ->toArray();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->refresh();
    }
}
