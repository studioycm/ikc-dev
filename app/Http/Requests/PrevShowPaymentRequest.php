<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'DataID' => ['required', 'integer'],
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'SagirID' => ['nullable', 'numeric'],
            'RegistrationID' => ['nullable', 'numeric'],
            'DogID' => ['nullable', 'numeric'],
            'PaymentAmount' => ['nullable', 'numeric'],
            'Last4Digits' => ['nullable'],
            'OwnerSocialID' => ['nullable'],
            'NameOnCard' => ['nullable'],
            'BuyerIP' => ['nullable'],
            'PaymentSubject' => ['nullable'],
            'CartKey' => ['nullable'],
            'PaymentStatus' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
