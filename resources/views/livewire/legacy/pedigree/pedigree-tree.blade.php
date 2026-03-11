@php
    $pedigree = $this->pedigreeData;
    $loadingTargets = 'loadPedigree,submitSettings,resetCertificateSettings,retryLoadPedigree';
    $isRtl = $this->direction === 'rtl';
@endphp

<div wire:init="loadPedigree" class="space-y-6">
    <form wire:submit.prevent="submitSettings" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="text-xs text-gray-500 dark:text-gray-400">
                @if ($this->depth >= 8)
                    {{ __('Large pedigree depths can take noticeably longer to build, especially when ancestor titles are enabled.') }}
                @else
                    {{ __('Apply the settings to rebuild the pedigree certificate.') }}
                @endif
            </div>

            <div class="flex items-center gap-3">
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="resetCertificateSettings"
                >
                    {{ __('Reset') }}
                </x-filament::button>

                <x-filament::button type="submit">
                    {{ __('Apply') }}
                </x-filament::button>
            </div>
        </div>
    </form>

    @if ($this->loadError)
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Pedigree build error') }}
            </x-slot>

            <div
                class="space-y-4 rounded-xl border border-danger-200 bg-danger-50 px-4 py-4 text-sm text-danger-700 dark:border-danger-900/50 dark:bg-danger-950/30 dark:text-danger-300">
                <div>{{ $this->loadError }}</div>

                <div class="flex flex-wrap items-center gap-3">
                    <x-filament::button
                        type="button"
                        color="danger"
                        wire:click="retryLoadPedigree"
                    >
                        {{ __('Retry') }}
                    </x-filament::button>

                    <span class="text-xs opacity-80">
                        {{ __('Tip: try fewer generations, compact density, or hide ancestor titles.') }}
                    </span>
                </div>

                @if (app()->environment('local') && filled($this->loadErrorTechnical))
                    <details
                        class="rounded-lg border border-danger-300/60 bg-white/60 p-3 text-xs text-danger-800 dark:border-danger-800 dark:bg-danger-950/30 dark:text-danger-200">
                        <summary class="cursor-pointer font-medium">
                            {{ __('Technical details') }}
                        </summary>

                        <div class="mt-2 break-words font-mono">
                            {{ $this->loadErrorTechnical }}
                        </div>
                    </details>
                @endif
            </div>
        </x-filament::section>
    @endif

    <div class="relative">
        <div
            wire:loading.delay
            wire:target="{{ $loadingTargets }}"
            class="absolute inset-0 z-30 flex items-center justify-center rounded-2xl bg-white/80 backdrop-blur-sm dark:bg-gray-950/75"
        >
            <div class="mx-auto flex max-w-md flex-col items-center px-6 py-10 text-center">
                <svg class="h-24 w-24" viewBox="0 0 160 120" fill="none" aria-hidden="true">
                    <g stroke="currentColor" class="text-primary-500 dark:text-primary-400" stroke-width="4"
                       stroke-linecap="round">
                        <path d="M20 60 H70" opacity="0.7">
                            <animate attributeName="opacity" values="0.2;1;0.2" dur="1.6s" repeatCount="indefinite"/>
                        </path>
                        <path d="M90 20 H140" opacity="0.4">
                            <animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.2s"
                                     repeatCount="indefinite"/>
                        </path>
                        <path d="M90 100 H140" opacity="0.4">
                            <animate attributeName="opacity" values="0.4;1;0.4" dur="1.6s" begin="0.4s"
                                     repeatCount="indefinite"/>
                        </path>
                        <path d="M80 60 V20" opacity="0.6">
                            <animate attributeName="opacity" values="0.3;1;0.3" dur="1.6s" begin="0.15s"
                                     repeatCount="indefinite"/>
                        </path>
                        <path d="M80 60 V100" opacity="0.6">
                            <animate attributeName="opacity" values="0.3;1;0.3" dur="1.6s" begin="0.35s"
                                     repeatCount="indefinite"/>
                        </path>
                    </g>

                    <g fill="currentColor" class="text-primary-600 dark:text-primary-300">
                        <circle cx="20" cy="60" r="10">
                            <animate attributeName="r" values="9;11;9" dur="1.6s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="80" cy="60" r="12">
                            <animate attributeName="r" values="11;13;11" dur="1.6s" begin="0.15s"
                                     repeatCount="indefinite"/>
                        </circle>
                        <circle cx="140" cy="20" r="10">
                            <animate attributeName="r" values="9;11;9" dur="1.6s" begin="0.25s"
                                     repeatCount="indefinite"/>
                        </circle>
                        <circle cx="140" cy="100" r="10">
                            <animate attributeName="r" values="9;11;9" dur="1.6s" begin="0.45s"
                                     repeatCount="indefinite"/>
                        </circle>
                    </g>
                </svg>

                <div class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('Building pedigree') . "..." }}
                </div>

                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @if ($this->depth >= 8)
                        {{ __('This depth is heavy. The request may take longer than usual.') }}
                    @else
                        {{ __('Preparing dog information and aligned ancestor grid') }}
                    @endif
                </div>
            </div>
        </div>

        @if (($pedigree['root'] ?? null) !== null)
            <div wire:loading.class="opacity-40" wire:target="{{ $loadingTargets }}"
                 class="space-y-6 transition-opacity">
                @include('livewire.legacy.pedigree.partials.pedigree-certificate-header', [
                    'root' => $pedigree['root'],
                    'depth' => $pedigree['depth'],
                    'rootTitlesMode' => $this->rootTitlesMode,
                ])

                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Pedigree Tree') }}
                    </x-slot>

                    <x-slot name="description">
                    </x-slot>

                    <div class="space-y-4">
                        <div
                            class="grid gap-3"
                            style="grid-template-columns: repeat({{ $pedigree['column_count'] }}, minmax(0, 1fr));"
                        >
                            @foreach ($pedigree['generation_headers'] as $header)
                                <div
                                    class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-center text-xs font-semibold text-gray-600 dark:border-white/10 dark:bg-white/5 dark:text-gray-300"
                                    style="grid-column: {{ $header['column_start'] }};"
                                >
                                    {{ $header['label'] }}
                                </div>
                            @endforeach
                        </div>

                        <div class="overflow-x-auto">
                            <div
                                style="min-width: {{ max(1100, $pedigree['column_count'] * ($this->density === 'compact' ? 220 : 255)) }}px;"
                            >
                                <div
                                    class="grid gap-x-4 gap-y-3"
                                    dir="{{ $this->direction }}"
                                    style="
                                        --pedigree-row-height: {{ $this->treeRowHeight() }};
                                        --pedigree-col-min: {{ $this->treeColumnMinWidth() }};
                                        grid-template-columns: repeat({{ $pedigree['column_count'] }}, minmax(var(--pedigree-col-min), 1fr));
                                        grid-template-rows: repeat({{ $pedigree['row_count'] }}, minmax(0, var(--pedigree-row-height)));
                                    "
                                >
                                    @foreach ($pedigree['nodes'] as $node)
                                        <div
                                            wire:key="pedigree-node-{{ $node['key'] }}"
                                            class="relative"
                                            style="
                                                grid-column: {{ $node['column_start'] }};
                                                grid-row: {{ $node['row_start'] }} / span {{ $node['row_span'] }};
                                            "
                                        >
                                            @include('livewire.legacy.pedigree.partials.pedigree-certificate-node', [
                                                'node' => $node,
                                                'visibleFields' => $this->visibleNodeFields,
                                                'density' => $this->density,
                                                'fontScale' => $this->fontScale,
                                                'showPlaceholders' => $this->showPlaceholders,
                                                'isRtl' => $isRtl,
                                                'maxGeneration' => $pedigree['depth'],
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>
        @elseif ($this->hasLoaded && ! $this->loadError)
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Pedigree Missing') }}
                </x-slot>

                <div
                    class="rounded-xl border border-danger-200 bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:border-danger-900/50 dark:bg-danger-950/30 dark:text-danger-300">
                    {{ __('Dog record was not found, so the pedigree certificate cannot be rendered.') }}
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div
                    class="min-h-[18rem] rounded-2xl border border-dashed border-gray-200 bg-gray-50/50 dark:border-white/10 dark:bg-white/5"></div>
            </x-filament::section>
        @endif
    </div>
</div>
