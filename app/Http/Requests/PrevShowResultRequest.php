<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowResultRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'RegDogID' => ['nullable', 'integer'],
            'SagirID' => ['nullable', 'integer'],
            'JudgeName' => ['nullable'],
            'ShowOrderID' => ['nullable', 'integer'],
            'MainArenaID' => ['nullable', 'integer'],
            'SubArenaID' => ['nullable', 'integer'],
            'ClassID' => ['nullable', 'integer'],
            'ShowID' => ['nullable', 'integer'],
            'JCAC' => ['nullable', 'integer'],
            'GCAC' => ['nullable', 'integer'],
            'REJCAC' => ['nullable', 'integer'],
            'REGCAC' => ['nullable', 'integer'],
            'CW' => ['nullable', 'integer'],
            'BJ' => ['nullable', 'integer'],
            'BV' => ['nullable', 'integer'],
            'CAC' => ['nullable', 'integer'],
            'RECACIB' => ['nullable', 'integer'],
            'RECAC' => ['nullable', 'integer'],
            'BP' => ['nullable', 'integer'],
            'BB' => ['nullable', 'integer'],
            'BOB' => ['nullable', 'integer'],
            'Excellent' => ['nullable', 'integer'],
            'Cannotbejudged' => ['nullable', 'integer'],
            'VeryGood' => ['nullable', 'integer'],
            'VeryPromising' => ['nullable', 'integer'],
            'Good' => ['nullable', 'integer'],
            'Promising' => ['nullable', 'integer'],
            'Sufficient' => ['nullable', 'integer'],
            'Satisfactory' => ['nullable', 'integer'],
            'Disqualified' => ['nullable', 'integer'],
            'Remarks' => ['nullable'],
            'Rank' => ['nullable', 'integer'],
            'CACIB' => ['nullable', 'integer'],
            'BD' => ['nullable', 'integer'],
            'BOS' => ['nullable', 'integer'],
            'BPIS' => ['nullable', 'integer'],
            'BPIS2' => ['nullable', 'integer'],
            'BPIS3' => ['nullable', 'integer'],
            'BJIS' => ['nullable', 'integer'],
            'BJIS2' => ['nullable', 'integer'],
            'BJIS3' => ['nullable', 'integer'],
            'BVIS' => ['nullable', 'integer'],
            'BVIS2' => ['nullable', 'integer'],
            'BVIS3' => ['nullable', 'integer'],
            'BIG' => ['nullable', 'integer'],
            'BIG2' => ['nullable', 'integer'],
            'BIG3' => ['nullable', 'integer'],
            'BIS' => ['nullable', 'integer'],
            'BIS2' => ['nullable', 'integer'],
            'BIS3' => ['nullable', 'integer'],
            'BreedID' => ['nullable', 'integer'],
            'NotPresent' => ['nullable', 'integer'],
            'GenderID' => ['nullable', 'integer'],
            'NoTitle' => ['nullable', 'integer'],
            'VCAC' => ['nullable', 'integer'],
            'RVCAC' => ['nullable', 'integer'],
            'BBaby' => ['nullable', 'integer'],
            'BBIS' => ['nullable', 'integer'],
            'BBIS2' => ['nullable', 'integer'],
            'BBIS3' => ['nullable', 'integer'],
            'BBaby2' => ['nullable', 'integer'],
            'BBaby3' => ['nullable', 'integer'],
            'VCACIB' => ['nullable', 'integer'],
            'JCACIB' => ['nullable', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
