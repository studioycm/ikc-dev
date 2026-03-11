<div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
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
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400">
                    <th class="px-3 py-2">{{ __('Check') }}</th>
                    <th class="px-3 py-2">{{ __('Result') }}</th>
                    <th class="px-3 py-2">{{ __('Value') }}</th>
                    <th class="px-3 py-2">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach (data_get($this->report, 'checks', []) as $row)
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">
                            {{ $row['label'] }}
                        </td>

                        <td class="px-3 py-2">
                                <span @class([
                                    'inline-flex rounded-md px-2 py-1 text-xs font-medium',
                                    'bg-success-50 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $row['color'] === 'success',
                                    'bg-danger-50 text-danger-700 dark:bg-danger-500/20 dark:text-danger-400' => $row['color'] === 'danger',
                                    'bg-warning-50 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' => $row['color'] === 'warning',
                                    'bg-gray-50 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400' => ! in_array($row['color'], ['success', 'danger', 'warning']),
                                ])>
                                    {{ $row['state_label'] }}
                                </span>
                        </td>

                        <td class="px-3 py-2 text-gray-600 dark:text-gray-300">
                            {{ is_scalar($row['value']) || $row['value'] === null ? ($row['value'] ?: '—') : '—' }}
                        </td>

                        <td class="px-3 py-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($row['actions'] as $action)
                                    {{
                                        $this->configuredAction(
                                            $action['key'] . '_' . $row['key'],
                                            $action['modal_heading'],
                                            $action['modal_description'],
                                        )
                                            ->icon($action['icon'])
                                            ->color($action['color'])
                                            ->label($action['label'])
                                    }}
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
                class="mt-4 rounded-lg border border-danger-200 bg-danger-50 p-3 text-sm text-danger-700 dark:border-danger-500/50 dark:bg-danger-500/20 dark:text-danger-400">
                {{ __('This dog has at least one blocking check.') }}
            </div>
        @elseif (data_get($this->report, 'summary.needs_review'))
            <div
                class="mt-4 rounded-lg border border-warning-200 bg-warning-50 p-3 text-sm text-warning-700 dark:border-warning-500/50 dark:bg-warning-500/20 dark:text-warning-400">
                {{ __('Some checks require office review or additional information.') }}
            </div>
        @endif
    @endif
</div>
