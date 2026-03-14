<x-filament::section>
    <x-slot name="heading">
        {{ __('Pedigree Tree') }}
    </x-slot>

    <x-slot name="description">
        {{ trans_choice('{1} Showing :count generation|[2,*] Showing :count generations', $depth, ['count' => $depth]) }}
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-[1fr,1.35fr,1fr]">
            <div class="space-y-3">
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Breed') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $root['breed_name'] ?: '—' }}
                    </div>
                    @if ($root['breed_name_secondary'])
                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $root['breed_name_secondary'] }}
                        </div>
                    @endif
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Gender') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $root['gender_label_raw'] ?: '—' }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Color') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $root['color_name'] ?: '—' }}
                    </div>
                    @if ($root['color_name_secondary'])
                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $root['color_name_secondary'] }}
                        </div>
                    @endif
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Birth Date') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $root['birth_date'] ?: '—' }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Registration Date') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $root['reg_date'] ?: '—' }}
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                <div class="space-y-3">
                    <div class="text-center text-2xl font-bold leading-tight text-gray-950 dark:text-white">
                        {{ $root['name_primary'] ?: '—' }}
                    </div>

                    @if ($root['name_secondary'])
                        <div class="text-center text-lg font-medium leading-tight text-gray-600 dark:text-gray-300">
                            {{ $root['name_secondary'] }}
                        </div>
                    @endif

                    @if ($root['breeding_house'])
                        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                            <x-filament::badge
                                size="lg"
                                color="primary"
                                icon="fas-house-user"
                                icon-position="before"
                                icon-size="md"
                                class="">
                                <span class="text-lg">{{ $root['breeding_house'] }}</span>
                            </x-filament::badge>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <span
                            class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-4 py-1.5 text-lg font-semibold text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-300">
                            {{ __('Sagir') }}: {{ $root['sagir_id'] ?: '—' }}
                        </span>

                        @if ($root['chip'])
                            <span
                                class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-4 py-1.5 text-sm font-semibold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                                {{ __('Chip') }}: {{ $root['chip'] }}
                            </span>
                        @endif
                    </div>

                    @if ($root['import_number'])
                        <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                            <span
                                class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-4 py-1.5 text-lg font-semibold text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-300">
                                {{ __('Import Number') }}: {{ $root['import_number'] ?: '—' }}
                            </span>
                        </div>
                    @endif

                    @if (filled($root['titles']))
                        @php
                            $compactTitles = array_slice($root['titles'], 0, 20);
                            $remainingTitles = max(0, $root['titles_count'] - count($compactTitles));
                        @endphp

                        <div x-data="{ expanded: @js($rootTitlesMode === 'expanded') }"
                             class="rounded-2xl border border-gray-200 bg-gray-50/70 px-5 py-4 dark:border-white/10 dark:bg-white/5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ trans_choice('{1} :count title|[2,*] :count titles', $root['titles_count'], ['count' => $root['titles_count']]) }}
                                    </div>
                                </div>

                                @if ($rootTitlesMode === 'compact' && $remainingTitles > 0)
                                    <button
                                        type="button"
                                        x-on:click="expanded = ! expanded"
                                        class="inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700 transition hover:border-primary-300 hover:text-primary-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:border-primary-700 dark:hover:text-primary-300"
                                    >
                            <span
                                x-show="! expanded">{{ __('Show all (:count more)', ['count' => $remainingTitles]) }}</span>
                                        <span x-show="expanded">{{ __('Show compact list') }}</span>
                                    </button>
                                @endif
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2" x-show="! expanded">
                                @foreach ($rootTitlesMode === 'compact' ? $compactTitles : $root['titles'] as $title)
                                    <span
                                        class="inline-flex items-center rounded-full border border-primary-200 bg-white px-3 py-1 text-xs font-medium text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/30 dark:text-primary-300">
                            {{ $title }}
                        </span>
                                @endforeach
                            </div>

                            @if ($rootTitlesMode === 'compact' && $remainingTitles > 0)
                                <div class="mt-4 flex flex-wrap justify-between gap-2" x-cloak x-show="expanded">
                                    @foreach ($root['titles'] as $title)
                                        <span
                                            class="inline-flex items-center rounded-full border border-primary-200 bg-white px-3 py-1 text-xs font-medium text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/30 dark:text-primary-300">
                                {{ $title }}
                            </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($root['pedigree_notes'])
                        <div
                            class="rounded-2xl border border-amber-200 bg-amber-50/80 px-5 py-4 dark:border-amber-900/40 dark:bg-amber-950/20">
                            <div
                                class="text-[11px] font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">
                                {{ __('Pedigree Notes') }}
                            </div>

                            <div class="mt-2 text-sm leading-6 text-amber-950 dark:text-amber-100">
                                {!! nl2br(e($root['pedigree_notes'])) !!}
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="space-y-3">
                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Breeder') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $root['breeder_text'] ?: '—' }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Owners') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $root['owner_names_text'] ?: '—' }}
                    </div>
                </div>

                <div
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                    <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('Owner Address') }}
                    </div>
                    <div class="mt-1 text-sm font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ $root['owner_address_display'] ?: '—' }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament::section>
