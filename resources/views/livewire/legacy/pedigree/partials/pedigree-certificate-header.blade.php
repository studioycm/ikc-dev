<x-filament::section>
    <x-slot name="heading">
        {{ __('Pedigree Tree') . ": " . $root['full_name'] }}
    </x-slot>

    <x-slot name="description">
        {{ __('Displaying') . " " . __(':n Generations', ['n' => $depth]) }}
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1fr,1.35fr,1fr]">
        <div class="space-y-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Breed') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['breed_name'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Gender') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['gender_label_raw'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Color') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['color_name'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Birth Date') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['birth_date'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Registration Date') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['reg_date'] ?: '—' }}
                </div>
            </div>
        </div>

        <div
            class="rounded-2xl border border-gray-200 bg-white px-6 py-5 text-center shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="space-y-2">
                <div class="text-2xl font-bold leading-tight text-gray-950 dark:text-white">
                    {{ $root['name_he'] ?: '—' }}
                </div>

                <div class="text-lg font-medium leading-tight text-gray-600 dark:text-gray-300">
                    {{ $root['name_en'] ?: '—' }}
                </div>

                @if ($root['breeding_house'])
                <div class="text-base font-semibold text-gray-950 dark:text-white">
                    {{ $root['breeding_house'] ?: '—' }}
                </div>
                @endif

                <div class="pt-2">
                    <span
                        class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-4 py-1.5 text-xl font-semibold text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-300">
                        {{ $root['sagir_id'] ?: '—' }}
                    </span>
                </div>
                @if ($root['import_number'])
                    <div class="pt-2">
                        <span
                            class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-4 py-1.5 text-sm font-semibold text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-300">
                            {{ __('Import Number') }}: {{ $root['import_number'] ?: '—' }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Owners') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">

                    {{ implode(', ', $root['owners']) }}

                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Pedigree Notes') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['pedigree_notes'] }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Titles') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ implode(', ', $root['titles']) }}
                </div>
            </div>
        </div>
    </div>
</x-filament::section>
