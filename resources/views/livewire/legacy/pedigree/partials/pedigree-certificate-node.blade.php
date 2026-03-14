@php
    $dog = $node['dog'];
    $titles = $dog['titles'] ?? [];
    $titlesText = $dog['titles_text'] ?? null;

    $fontMap = match ($fontScale) {
        'small' => [
            'name' => $density === 'compact' ? 'text-xs' : 'text-sm',
            'meta' => 'text-[11px]',
            'badge' => 'text-[10px]',
            'title' => 'text-[11px]',
        ],
        'large' => [
            'name' => $density === 'compact' ? 'text-base' : 'text-lg',
            'meta' => $density === 'compact' ? 'text-xs' : 'text-sm',
            'badge' => 'text-xs',
            'title' => $density === 'compact' ? 'text-xs' : 'text-sm',
        ],
        default => [
            'name' => $density === 'compact' ? 'text-sm' : 'text-base',
            'meta' => $density === 'compact' ? 'text-[11px]' : 'text-xs',
            'badge' => 'text-[10px]',
            'title' => $density === 'compact' ? 'text-[11px]' : 'text-xs',
        ],
    };

    $paddingClasses = $density === 'compact' ? 'p-2.5' : 'p-3.5';

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

    $metadataItems = [];

    if (($visibleFields['import_number'] ?? false) && $dog['import_number']) {
        $metadataItems[] = ['label' => __('Import'), 'value' => $dog['import_number']];
    }

    if (($visibleFields['breeding_house'] ?? false) && $dog['breeding_house']) {
        $metadataItems[] = ['label' => __('Kennel'), 'value' => $dog['breeding_house']];
    }

    if (($visibleFields['breed_name'] ?? false) && $dog['breed_name']) {
        $metadataItems[] = ['label' => __('Breed'), 'value' => $dog['breed_name']];
    }

    if (($visibleFields['color_name'] ?? false) && $dog['color_name']) {
        $metadataItems[] = ['label' => __('Color'), 'value' => $dog['color_name']];
    }

    if (($visibleFields['birth_date'] ?? false) && $dog['birth_date']) {
        $metadataItems[] = ['label' => __('D.O.B'), 'value' => $dog['birth_date']];
    }

    if (($visibleFields['age'] ?? false) && $dog['age']) {
        $metadataItems[] = ['label' => __('Age'), 'value' => $dog['age']];
    }

    $previousSide = $isRtl ? 'right-0' : 'left-0';
    $previousOutside = $isRtl ? '-right-4' : '-left-4';
    $nextSide = $isRtl ? 'left-0' : 'right-0';
    $nextOutside = $isRtl ? '-left-4' : '-right-4';
@endphp

<div class="relative h-full">
    @if ($node['generation'] >= 1)
        <div
            class="pointer-events-none absolute top-1/2 z-0 h-px w-4 -translate-y-1/2 bg-gray-300/90 dark:bg-white/15 {{ $previousOutside }}"></div>
        <div
            class="pointer-events-none absolute top-1/2 z-0 h-px w-4 -translate-y-1/2 bg-gray-300/90 dark:bg-white/15 {{ $previousSide }}"></div>
    @endif

    @if ($node['generation'] < $maxGeneration)
        <div
            class="pointer-events-none absolute top-1/2 z-0 h-px w-4 -translate-y-1/2 bg-gray-300/70 dark:bg-white/10 {{ $nextOutside }}"></div>
        <div
            class="pointer-events-none absolute top-1/2 z-0 h-px w-4 -translate-y-1/2 bg-gray-300/70 dark:bg-white/10 {{ $nextSide }}"></div>
    @endif

    @if ($node['is_placeholder'])
        <div
            class="relative z-10 h-full rounded-xl border border-dashed border-gray-200 bg-gray-50/70 dark:border-white/10 dark:bg-white/5">
            @if ($showPlaceholders)
                <div
                    class="flex h-full items-center justify-center px-3 text-center text-[11px] font-medium text-gray-400 dark:text-gray-500">
                    {{ __('Missing ancestor record') }}
                </div>
            @endif
        </div>
        @else
        <div class="relative z-10 h-full rounded-xl border shadow-sm {{ $cardClasses }} {{ $paddingClasses }}">
            <div class="grid h-full content-start gap-2.5">
                <div class="flex items-start justify-between gap-2">
                    <span
                        class="inline-flex items-center rounded-full px-2 py-0.5 font-semibold {{ $badgeClasses }} {{ $fontMap['badge'] }}">
                        {{ $dog['gender_label'] }}
                    </span>

                    @if (($visibleFields['sagir_id'] ?? false) && $dog['sagir_id'])
                        <span
                            class="rounded-md bg-white/80 px-2 py-0.5 font-medium text-gray-700 shadow-sm dark:bg-white/10 dark:text-gray-200 {{ $fontMap['badge'] }}">
                            {{ $dog['sagir_id'] }}
                        </span>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="font-semibold leading-tight text-gray-950 dark:text-white {{ $fontMap['name'] }}">
                        {{ $dog['name_primary'] ?: '—' }}
                    </div>

                    @if ($dog['name_secondary'])
                        <div class="leading-tight text-gray-700 dark:text-gray-300 {{ $fontMap['meta'] }}">
                            {{ $dog['name_secondary'] }}
                        </div>
                    @endif
                </div>

                @if (filled($metadataItems))
                    @if ($density === 'compact')
                        <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                            @foreach ($metadataItems as $item)
                                <div class="min-w-0 text-gray-600 dark:text-gray-300 {{ $fontMap['meta'] }}">
                                    <span class="font-medium">{{ $item['label'] }}:</span>
                                    <span class="break-words">{{ $item['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="space-y-1">
                            @foreach ($metadataItems as $item)
                                <div class="text-gray-600 dark:text-gray-300 {{ $fontMap['meta'] }}">
                                    <span class="font-medium">{{ $item['label'] }}:</span>
                                    <span>{{ $item['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                @if (($visibleFields['titles'] ?? false) && filled($titlesText))
                    <div
                        x-data="{
                            open: false,
                            placeAbove: true,
                            toggle() {
                                this.open = ! this.open;
                            }
                        }"
                        x-ref="host"
                        class="relative border-t border-gray-200/70 pt-2 dark:border-white/10"
                    >
                        <div class="flex items-start gap-2">
                            <div
                                class="min-w-0 flex-1 leading-4 text-gray-600 dark:text-gray-300 {{ $fontMap['title'] }}"
                                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"
                            >
                                {{ $titlesText }}
                            </div>

                            @if ($dog['titles_has_popup'])
                                <button
                                    type="button"
                                    x-on:click.stop="toggle()"
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:border-primary-300 hover:text-primary-600 dark:border-white/10 dark:bg-gray-900/70 dark:text-gray-300 dark:hover:border-primary-700 dark:hover:text-primary-300"
                                    title="{{ __('Show full title list') }}"
                                >
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16Zm.75-10.75a.75.75 0 10-1.5 0v.5a.75.75 0 001.5 0v-.5Zm0 3a.75.75 0 10-1.5 0v3a.75.75 0 001.5 0v-3Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        @if ($dog['titles_has_popup'])
                            <div
                                x-cloak
                                x-show="open"
                                x-ref="panel"
                                x-transition.opacity
                                x-on:click.outside="open = false"
                                class="absolute inset-x-0 z-20 rounded-xl border border-gray-200 bg-white p-3 shadow-2xl dark:border-white/10 dark:bg-gray-900"
                                :class="placeAbove ? 'bottom-full mb-2' : 'top-full mt-2'"
                            >
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">
                                    {{ __('Titles') }}
                                </div>

                                <div class="mt-2 text-xs leading-5 text-gray-700 dark:text-gray-200">
                                    {{ implode(', ', $titles) }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
