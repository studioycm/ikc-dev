<div class="space-y-6">
    <form wire:submit.prevent="applySettingsFromForm" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button
                type="button"
                color="gray"
                wire:click="resetCertificateSettings"
            >
                {{ __('Reset certificate settings') }}
            </x-filament::button>
        </div>
    </form>

    @if (! $this->pedigree['root'])
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Pedigree Tree') }}
            </x-slot>

            <div
                class="rounded-xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:border-danger-900/50 dark:bg-danger-950/30 dark:text-danger-300">
                {{ __('Dog record was not found, so the pedigree certificate cannot be rendered.') }}
            </div>
        </x-filament::section>
    @else
        @include('livewire.legacy.pedigree.partials.pedigree-certificate-header', [
            'root' => $this->pedigree['root'],
            'depth' => $this->pedigree['depth'],
        ])

        <x-filament::section>
            <x-slot name="heading">
            </x-slot>

            <div class="space-y-4">
                <div
                    class="grid gap-3"
                    style="grid-template-columns: repeat({{ $this->pedigree['column_count'] }}, minmax(0, 1fr));"
                >
                    @foreach ($this->pedigree['generation_headers'] as $header)
                        <div
                            class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:border-white/10 dark:bg-white/5 dark:text-gray-300"
                            style="grid-column: {{ $header['column_start'] }};"
                        >
                            {{ $header['label'] }}
                        </div>
                    @endforeach
                </div>

                <div class="overflow-x-auto">
                    <div class="min-w-[1100px]">
                        <div
                            class="pedigree-row grid gap-3"
                            dir="{{ $this->direction }}"
                            style="
                                --pedigree-row-height: {{ $this->density === 'compact' ? '8.75rem' : '13.25rem' }};
                                grid-template-columns: repeat({{ $this->pedigree['column_count'] }}, minmax(16rem, 1fr));
                                grid-template-rows: repeat({{ $this->pedigree['row_count'] }}, minmax(0, var(--pedigree-row-height)));
                            "
                        >
                            @foreach ($this->pedigree['nodes'] as $node)
                                <div
                                    wire:key="pedigree-node-{{ $node['key'] }}"
                                    style="
                                        grid-column: {{ $node['column_start'] }};
                                        grid-row: {{ $node['row_start'] }} / span {{ $node['row_span'] }};
                                    "
                                >
                                    @include('livewire.legacy.pedigree.partials.pedigree-certificate-node', [
                                        'node' => $node,
                                        'visibleFields' => $this->visibleNodeFields,
                                        'density' => $this->density,
                                        'showPlaceholders' => $this->showPlaceholders,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    @endif
</div>
