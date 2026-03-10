@php
    $dog = $node['dog'];

    $densityClasses = $density === 'compact'
        ? 'p-2 text-[11px]'
        : 'p-3 text-sm';

    $cardClasses = match ($dog['gender_value']) {
        1 => 'border-blue-200 bg-blue-50/40 dark:border-blue-900/50 dark:bg-blue-950/20',
        2 => 'border-pink-200 bg-pink-50/40 dark:border-pink-900/50 dark:bg-pink-950/20',
        default => 'border-gray-200 bg-white dark:border-white/10 dark:bg-white/5',
    };

    $badgeClasses = match ($dog['gender_value']) {
        1 => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
        2 => 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300',
        default => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
    };
@endphp

@if ($node['is_placeholder'])
    <div
        class="h-full rounded-xl border border-dashed border-gray-200 bg-gray-50/70 dark:border-white/10 dark:bg-white/5">
        @if ($showPlaceholders)
            <div
                class="flex h-full items-center justify-center px-3 text-center text-[11px] font-medium text-gray-400 dark:text-gray-500">
                {{ __('Missing ancestor record') }}
            </div>
        @endif
    </div>
@else
    <div class="h-full rounded-xl border shadow-sm {{ $cardClasses }} {{ $densityClasses }}">
        <div class="flex h-full flex-col justify-center gap-2">
            <div class="flex items-start justify-between gap-2">
                <span
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $badgeClasses }}">
                    {{ $dog['gender_label'] }}
                </span>

                <div class="text-[12px] bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300">
                    @if (($visibleFields['sagir_id'] ?? false) && $dog['sagir_id'])
                        {{ $dog['sagir_id'] }}
                    @endif
                </div>
            </div>

            <div class="min-h-0 space-y-1">

                <div class="flex items-start justify-between gap-2">
                    <div class="font-semibold leading-tight text-gray-950 dark:text-white">
                        {{ $dog['full_name'] }}
                    </div>
                </div>

                @if (($visibleFields['breeding_house'] ?? false) && $dog['breeding_house'])
                    <div class="leading-tight text-gray-500 dark:text-gray-400">
                        {{ $dog['breeding_house'] }}
                    </div>
                @endif

                @if (($visibleFields['import_number'] ?? false) && $dog['import_number'])
                    <div class="leading-tight text-gray-500 dark:text-gray-400">
                        {{ __('Import') }}: {{ $dog['import_number'] }}
                    </div>
                @endif


                @if (($visibleFields['breed_name'] ?? false) && $dog['breed_name'])
                    <div class="font-semibold leading-tight text-gray-500 dark:text-gray-400">
                        {{ $dog['breed_name'] }}
                    </div>
                @endif
                <div class="flex items-start justify-between gap-2">
                @if (($visibleFields['color_name'] ?? false) && $dog['color_name'])
                    <div class="leading-tight text-gray-500 dark:text-gray-400">
                        {{ $dog['color_name'] }}
                    </div>
                @endif

                @if (($visibleFields['birth_date'] ?? false) && $dog['birth_date'])
                    <div class="leading-tight text-gray-500 dark:text-gray-400">
                        {{ $dog['birth_date'] }}
                    </div>
                @endif

                    @if (($visibleFields['age'] ?? false) && $dog['age'])
                        <div class="leading-tight text-gray-500 dark:text-gray-400">
                            {{ $dog['age'] }}
                        </div>
                    @endif
                </div>
            </div>

            @if (($visibleFields['titles'] ?? false) && filled($dog['titles']))
                <div title="{{ implode(', ', $dog['titles']) }}"
                    class="border-t border-gray-200/70 pt-2 text-[12px] leading-4 text-gray-500 dark:border-white/10 dark:text-gray-400">
                    {{ implode(', ', array_slice($dog['titles'], 0, 10)) }}
                </div>
            @endif
        </div>
    </div>
@endif
