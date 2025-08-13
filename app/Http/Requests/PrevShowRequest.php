<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'TitleName' => ['nullable'],
            'StartDate' => ['nullable', 'date'],
            'ShortDesc' => ['nullable'],
            'LongDesc' => ['nullable'],
            'TopImage' => ['nullable'],
            'MaxRegisters' => ['nullable', 'numeric'],
            'ShowType' => ['nullable', 'numeric'],
            'ClubID' => ['nullable', 'numeric'],
            'EndRegistrationDate' => ['nullable', 'date'],
            'ShowStatus' => ['nullable', 'numeric'],
            'EndDate' => ['nullable', 'date'],
            'ShowPrice' => ['nullable', 'numeric'],
            'Dog2Price1' => ['required', 'numeric'],
            'Dog2Price2' => ['required', 'numeric'],
            'Dog2Price3' => ['required', 'numeric'],
            'Dog2Price4' => ['required', 'numeric'],
            'Dog2Price5' => ['required', 'numeric'],
            'Dog2Price6' => ['required', 'numeric'],
            'Dog2Price7' => ['required', 'numeric'],
            'Dog2Price8' => ['required', 'numeric'],
            'Dog2Price9' => ['required', 'numeric'],
            'Dog2Price10' => ['required', 'numeric'],
            'CouplesPrice' => ['nullable', 'numeric'],
            'BGidulPrice' => ['nullable', 'numeric'],
            'ZezaimPrice' => ['nullable', 'numeric'],
            'YoungPrice' => ['nullable', 'numeric'],
            'MoreDogsPrice' => ['nullable', 'numeric'],
            'MoreDogsPrice2' => ['nullable', 'numeric'],
            'TicketCost' => ['nullable', 'numeric'],
            'IsExtraTickets' => ['nullable'],
            'IsParking' => ['nullable'],
            'MoreTicketsSelect' => ['nullable'],
            'ParkingSelect' => ['nullable'],
            'PeototCost' => ['nullable', 'numeric'],
            'FreeTextDesc' => ['nullable'],
            'start_from_index' => ['nullable'],
            'location' => ['nullable'],
            'banner_image' => ['nullable'],
            'Check_all_members' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
