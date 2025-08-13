<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowClassRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'ClassName' => ['nullable'],
            'Age_FromMonths' => ['nullable', 'numeric'],
            'Age_TillMonths' => ['nullable', 'numeric'],
            'SpecialClassID' => ['nullable', 'numeric'],
            'HairID' => ['nullable', 'numeric'],
            'ColorID' => ['nullable', 'numeric'],
            'ShowRaceID' => ['nullable', 'numeric'],
            'ShowID' => ['nullable', 'exists:ShowsDB'],
            'ShowArenaID' => ['nullable', 'exists:Shows_Structure'],
            'Remarks' => ['nullable'],
            'Status' => ['nullable', 'numeric'],
            'OrderID' => ['nullable', 'numeric'],
            'IsChampClass' => ['nullable', 'numeric'],
            'IsWorkingClass' => ['nullable', 'numeric'],
            'IsOpenClass' => ['nullable', 'numeric'],
            'IsVeteranClass' => ['nullable', 'numeric'],
            'GenderID' => ['nullable', 'numeric'],
            'BreedID' => ['nullable', 'numeric'],
            'ShowMainArenaID' => ['nullable', 'numeric'],
            'AwardIDClass' => ['nullable', 'numeric'],
            'IsCouplesClass' => ['nullable', 'numeric'],
            'IsZezaimClass' => ['nullable', 'numeric'],
            'IsYoungDriverClass' => ['nullable', 'numeric'],
            'IsBgidulClass' => ['nullable', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
