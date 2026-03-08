<x-filament::section>
    <x-slot name="heading">
        {{ __('Pedigree certificate') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Main dog information is separated as a full certificate header before the aligned ancestor tree.') }}
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
                    {{ $root['gender_label'] ?: '—' }}
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
                    {{ __('Birth date') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['birth_date'] ?: '—' }}
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

                <div class="pt-1 text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Kennel / בית-גידול') }}
                </div>

                <div class="text-base font-semibold text-gray-950 dark:text-white">
                    {{ $root['breeding_house'] ?: '—' }}
                </div>

                <div class="pt-2">
                    <span
                        class="inline-flex items-center rounded-full border border-primary-200 bg-primary-50 px-4 py-1.5 text-sm font-semibold text-primary-700 dark:border-primary-900/60 dark:bg-primary-950/40 dark:text-primary-300">
                        {{ __('Sagir-ID') }}: {{ $root['sagir_id'] ?: '—' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Import number') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['import_number'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Father Sagir-ID') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['father_sagir'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Mother Sagir-ID') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $root['mother_sagir'] ?: '—' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                <div class="text-[11px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('Displayed ancestor generations') }}
                </div>
                <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                    {{ $depth }}
                </div>
            </div>
        </div>
    </div>
</x-filament::section>
