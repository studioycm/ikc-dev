<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowBreedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'RaceID' => ['nullable', 'exists:BreedsDB'],
            'ArenaID' => ['nullable', 'exists:Shows_Structure'],
            'Remarks' => ['nullable'],
            'OrderID' => ['nullable', 'integer'],
            'ShowID' => ['nullable', 'exists:ShowsDB'],
            'MainArenaID' => ['nullable', 'exists:Shows_Structure'],
            'JudgeID' => ['nullable', 'exists:JudgesDB'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
