<div
    class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
    dir="{{ $this->direction }}"
    wire:key="{{ $role }}-dog-checks-{{ $sagirId }}">


@if ($title !== '')
        <div class="mb-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </div>
    @endif

    @if (! $this->report)
        <div class="text-sm text-gray-400 dark:text-gray-500">
            {{ __('No dog selected.') }}
        </div>
    @else
        <div class="mb-4">
            <div class="font-semibold text-gray-900 dark:text-gray-100">
                {{ data_get($this->report, 'dog.name') ?: '—' }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ data_get($this->report, 'dog.sagir_id') ?: '—' }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                <tr class="text-{{ $this->direction === 'rtl' ? 'right' : 'left' }} text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <th class="w-7/12 px-3 py-2 font-medium">{{ __('Check') }}</th>
                    <th class="w-1/4 px-3 py-2 font-medium">{{ __('Status') }}</th>
                    <th class="w-1/6 px-3 py-2 font-medium text-{{ $this->direction === 'rtl' ? 'left' : 'right' }}">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach (data_get($this->report, 'checks', []) as $row)
                    @php
                        $statusIcon = match($row['state']) {
                            'absolute_yes' => 'heroicon-m-check-circle',
                            'absolute_no' => 'heroicon-m-x-circle',
                            'check_needed' => 'heroicon-m-exclamation-triangle',
                            default => 'heroicon-m-question-mark-circle',
                        };
                    @endphp

                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-3 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $row['label'] }}
                        </td>

                        <td class="px-3 py-3">
                            <span
                                x-data
                                x-tooltip="{
                                    content: @js($this->getTooltipContent($row)),
                                    theme: $store.theme === 'dark' ? 'dark' : 'light',
                                }"
                                @class([
                                    'inline-flex items-center justify-center rounded-full p-1.5 cursor-help transition-all',
                                    'bg-success-100 text-success-600 dark:bg-success-500/20 dark:text-success-400' => $row['color'] === 'success',
                                    'bg-danger-100 text-danger-600 dark:bg-danger-500/20 dark:text-danger-400' => $row['color'] === 'danger',
                                    'bg-warning-100 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400' => $row['color'] === 'warning',
                                    'bg-gray-100 text-gray-600 dark:bg-gray-500/20 dark:text-gray-400' => ! in_array($row['color'], ['success', 'danger', 'warning']),
                                ])
                            >
                                <x-filament::icon
                                    :icon="$statusIcon"
                                    class="h-5 w-5"
                                />
                            </span>
                        </td>

                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-1">
                                {{-- Info/Details Button --}}
                                <button
                                    type="button"
                                    wire:click="mountAction('viewDetails', { checkKey: '{{ $row['key'] }}' })"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                    x-tooltip="{ content: @js(__('View details')), theme: $store.theme === 'dark' ? 'dark' : 'light' }"
                                >
                                    <x-filament::icon
                                        icon="heroicon-m-information-circle"
                                        class="h-5 w-5"
                                    />
                                </button>

                                {{-- Additional Actions (DNA test, etc.) --}}
                                @foreach ($row['actions'] as $action)
                                    <button
                                        type="button"
                                        wire:click="mountAction('rowAction', { actionKey: '{{ $action['key'] }}_{{ $row['key'] }}', heading: '{{ $action['modal_heading'] }}', description: '{{ $action['modal_description'] }}' })"
                                        @class([
                                            'flex h-8 w-8 items-center justify-center rounded-lg transition',
                                            'text-warning-400 hover:bg-warning-100 hover:text-warning-500 dark:hover:bg-warning-500/20 dark:hover:text-warning-300' => $action['color'] === 'warning',
                                            'text-gray-400 hover:bg-gray-100 hover:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-gray-300' => $action['color'] === 'gray',
                                        ])
                                        x-tooltip="{ content: @js($action['label']), theme: $store.theme === 'dark' ? 'dark' : 'light' }"
                                    >
                                        <x-filament::icon
                                            :icon="$action['icon']"
                                            class="h-5 w-5"
                                        />
                                    </button>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if (data_get($this->report, 'summary.blocking'))
                <div
                    class="mt-4 flex items-start gap-3 rounded-lg border border-danger-200 bg-danger-50 p-3 text-sm text-danger-700 dark:border-danger-500/50 dark:bg-danger-500/20 dark:text-danger-400">
                    <x-filament::icon icon="heroicon-m-exclamation-circle" class="mt-0.5 h-5 w-5 flex-shrink-0"/>
                    <span>{{ __('This dog has at least one blocking check.') }}</span>
            </div>
        @elseif (data_get($this->report, 'summary.needs_review'))
                <div
                    class="mt-4 flex items-start gap-3 rounded-lg border border-warning-200 bg-warning-50 p-3 text-sm text-warning-700 dark:border-warning-500/50 dark:bg-warning-500/20 dark:text-warning-400">
                    <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0"/>
                    <span>{{ __('Some checks require office review or additional information.') }}</span>
            </div>
        @endif
    @endif

    <x-filament-actions::modals/>
</div>
