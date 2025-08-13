<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowDogRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'ShowID' => ['nullable', 'exists:ShowsDB'],
            'SagirID' => ['nullable', 'exists:DogsDB'],
            'GlobalSagirID' => ['nullable'],
            'OrderID' => ['nullable', 'integer'],
            'OwnerID' => ['nullable', 'exists:users'],
            'BirthDate' => ['nullable', 'date'],
            'BreedID' => ['nullable', 'exists:BreedsDB'],
            'SizeID' => ['nullable', 'integer'],
            'GenderID' => ['nullable', 'integer'],
            'DogName' => ['nullable'],
            'ShowRegistrationID' => ['nullable', 'exists:shows_registration'],
            'ClassID' => ['nullable', 'exists:Shows_Classes'],
            'OwnerName' => ['nullable'],
            'OwnerMobile' => ['nullable'],
            'BeitGidulName' => ['nullable'],
            'HairID' => ['nullable'],
            'ColorID' => ['nullable'],
            'MainArenaID' => ['nullable', 'integer'],
            'ArenaID' => ['nullable', 'exists:Shows_Structure'],
            'ShowBreedID' => ['nullable', 'integer'],
            'MobileNumber' => ['nullable'],
            'OwnerEmail' => ['nullable'],
            'new_show_registration_id' => ['nullable', 'exists:shows_registration'],
            'present' => ['nullable', 'date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
