<div class="rounded-xl border bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    @if ($title !== '')
        <div class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </div>
    @endif

    @if ($this->badges === [])
        <div class="text-sm text-gray-400 dark:text-gray-500">
            {{ __('No eligible membership types found.') }}
        </div>
    @else
        <div class="flex flex-wrap gap-2">
            @foreach ($this->badges as $badge)
                <span @class([
                    'inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium',
                    'bg-success-50 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $badge['color'] === 'success',
                    'bg-info-50 text-info-700 dark:bg-info-500/20 dark:text-info-400' => $badge['color'] === 'info',
                    'bg-warning-50 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' => $badge['color'] === 'warning',
                    'bg-gray-50 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400' => ! in_array($badge['color'], ['success', 'info', 'warning']),
                ])>
                    <x-filament::icon :icon="$badge['icon']" class="h-4 w-4"/>
                    {{ $badge['label'] }}
                </span>
            @endforeach
        </div>
    @endif
</div>
