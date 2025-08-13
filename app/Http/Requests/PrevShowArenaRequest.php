<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowArenaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'ShowID' => ['nullable', 'integer'],
            'GroupName' => ['nullable'],
            'GroupParentID' => ['nullable', 'integer'],
            'ClassID' => ['nullable', 'integer'],
            'OrderID' => ['nullable', 'integer'],
            'ArenaType' => ['nullable', 'integer'],
            'ManagerPass' => ['nullable'],
            'JudgeID' => ['nullable', 'integer'],
            'arena_date' => ['nullable', 'date'],
            'OrderTime' => ['nullable', 'date'],
            'ShowsDB_id' => ['required', 'exists:ShowsDB'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
