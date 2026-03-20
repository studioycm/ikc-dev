@php
    $displayNumber = $dog->ImportNumber ?: $dog->SagirID;
    $birth = $dog->BirthDate?->format('d/m/Y');
    $color = $dog->color?->ColorNameHE ?: __('Missing Color');
    $gender = $dog->GenderID->value === 1 ? __('Sire') : __('Dam');
    $titles = $dog->titles?->pluck('TitleName')->take(10);
    $hasMoreTitles = $dog->titles?->count() > 10;

    $genderColor = match ($dog->GenderID) {
        1 => 'bg-blue-100 text-blue-700',
        2 => 'bg-pink-100 text-pink-700',
        default => 'bg-gray-100 text-gray-600',
    };
@endphp

<div class="bg-white rounded-lg border shadow-sm p-3 text-sm">

    <div class="flex justify-between items-center mb-2">

        <span class="px-2 py-0.5 rounded text-xs {{ $genderColor }}">
            {{ $gender }}
        </span>


    </div>

    <div class="font-semibold">
        {{ $dog->full_name ?: '—' }}
    </div>

    <div class="text-xs text-gray-500">
        {{ $displayNumber }}
    </div>

    <div class="text-xs text-gray-500">
        {{ $color }}
    </div>

    @if($birth)
        <div class="text-xs text-gray-500">
            {{ $birth }}
        </div>
    @endif

    @if($titles && $titles->isNotEmpty())
        <div class="text-xs mt-2">
            {{ $titles->implode(', ') }}
            @if($hasMoreTitles)
                …
            @endif
        </div>
    @endif

</div>
