@php
    use App\Filament\Resources\PrevJudgeResource;
@endphp

<div class="p-4">
    @if (!empty($record?->judge))
        <div class="space-y-2">
            <div class="text-lg font-semibold">{{ $record->judge->JudgeNameHE }} ({{ $record->judge->JudgeNameEN }})
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div><span class="font-medium">{{ __('Country') }}:</span> {{ $record->judge->Country ?? '—' }}</div>
                <div><span class="font-medium">{{ __('Email') }}:</span> {{ $record->judge->Email ?? '—' }}</div>
                <div><span class="font-medium">{{ __('DataID') }}:</span> {{ $record->judge->DataID }}</div>
            </div>
            <div>
                <a
                    href="{{ PrevJudgeResource::getUrl('view', ['record' => $record->judge->getKey()]) }}"
                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    {{ __('Open judge page') }}
                </a>
            </div>
        </div>
    @else
        <div class="text-gray-500">{{ __('No judge assigned to this arena.') }}</div>
    @endif
</div>
