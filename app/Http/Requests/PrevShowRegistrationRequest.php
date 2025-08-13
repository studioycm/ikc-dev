<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrevShowRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ModificationDateTime' => ['nullable', 'date'],
            'CreationDateTime' => ['nullable', 'date'],
            'DogId' => ['nullable', 'integer'],
            'DogName' => ['nullable'],
            'BreedID' => ['nullable', 'integer'],
            'ColorID' => ['nullable', 'integer'],
            'HairID' => ['nullable', 'integer'],
            'SizeID' => ['nullable', 'integer'],
            'BirthDate' => ['nullable', 'date'],
            'GlobalSagirID' => ['nullable'],
            'GenderID' => ['nullable', 'integer'],
            'Owner_Phone' => ['nullable'],
            'Owner_City' => ['nullable'],
            'Owner_Street' => ['nullable'],
            'Owner_StNumber' => ['nullable'],
            'Owner_Zip' => ['nullable'],
            'Owner_Email' => ['nullable'],
            'Owner_Mobile' => ['nullable'],
            'Owner_IsMember' => ['nullable', 'integer'],
            'SpecialKey' => ['nullable'],
            'SpecialClass' => ['nullable', 'integer'],
            'Owner_FirstName' => ['nullable'],
            'Owner_LastName' => ['nullable'],
            'Status' => ['nullable', 'integer'],
            'SagirID' => ['nullable', 'exists:DogsDB'],
            'Couples1_MDogName' => ['nullable'],
            'Couples1_MSagirID' => ['nullable'],
            'Couples2_FDogName' => ['nullable'],
            'Couples2_FSagirID' => ['nullable'],
            'bGidul1_DogName' => ['nullable'],
            'bGidul2_DogName' => ['nullable'],
            'bGidul3_DogName' => ['nullable'],
            'bGidul4_DogName' => ['nullable'],
            'bGidul5_DogName' => ['nullable'],
            'bGidul1_SagirID' => ['nullable'],
            'bGidul2_SagirID' => ['nullable'],
            'bGidul3_SagirID' => ['nullable'],
            'bGidul4_SagirID' => ['nullable'],
            'bGidul5_SagirID' => ['nullable'],
            'Gor1_DogName' => ['nullable'],
            'Gor2_DogName' => ['nullable'],
            'Gor3_DogName' => ['nullable'],
            'Gor4_DogName' => ['nullable'],
            'Gor5_DogName' => ['nullable'],
            'Gor1_SagirID' => ['nullable'],
            'Gor2_SagirID' => ['nullable'],
            'Gor3_SagirID' => ['nullable'],
            'Gor4_SagirID' => ['nullable'],
            'Gor5_SagirID' => ['nullable'],
            'Young_FullName' => ['nullable'],
            'YoungBirthDate' => ['nullable'],
            'Young_Address' => ['nullable'],
            'Young_Phone' => ['nullable'],
            'Young_BreedID' => ['nullable'],
            'Notes' => ['nullable'],
            'ShowID' => ['nullable', 'exists:ShowsDB'],
            'IsbillingOK' => ['nullable', 'integer'],
            'IsPedigreeOK' => ['nullable', 'integer'],
            'IsManagerOK' => ['nullable', 'integer'],
            'InvoiceID' => ['nullable', 'integer'],
            'PrePayStatus' => ['nullable'],
            'invoice_text' => ['nullable'],
            'Gor_Parent_SagirID' => ['nullable'],
            'bGidul6_SagirID' => ['nullable'],
            'ClassID' => ['nullable', 'exists:Shows_Classes'],
            'registered_by' => ['nullable', 'exists:users'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
