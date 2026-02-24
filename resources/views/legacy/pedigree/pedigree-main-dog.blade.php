@php
    $displayNumber = $dog->ImportNumber ?: $dog->SagirID;
    $breed = $dog->breed?->BreedName ?: __('Missing Breed');
    $color = $dog->color?->ColorNameHE ?: __('Missing Color');
@endphp

<div class="bg-white rounded-xl shadow p-6 border">
    <div class="flex justify-between items-start">

        <div>
            <div class="text-xl font-bold">
                {{ $dog->full_name }}
            </div>

            <div class="text-sm text-gray-500">
                {{ $displayNumber }}
            </div>

            <div class="text-sm text-gray-500">
                {{ $breed }} / {{ $color }}
            </div>
        </div>


    </div>
</div>
