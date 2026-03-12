<div class="space-y-4">
    {{-- Dog Information --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Dog Information') }}
        </h4>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Name') }}</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dog['name'] ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Sagir ID') }}</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dog['sagir_id'] ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Breed') }}</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dog['breed'] ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Age') }}</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $dog['age_years'] ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Check Details --}}
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ $check['label'] }}
        </h4>

        <dl class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                <dd>
                    <x-filament::badge
                        :color="$check['color']"
                        :icon="$check['icon']"
                    >
                        {{ $check['state_label'] }}
                    </x-filament::badge>
                </dd>
            </div>

            @if(filled($check['value']))
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Value') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">
                        {{ is_scalar($check['value']) ? $check['value'] : '—' }}
                    </dd>
                </div>
            @endif

            @if($check['state'] === 'absolute_no')
                <div
                    class="rounded-lg bg-danger-50 p-3 text-sm text-danger-700 dark:bg-danger-500/10 dark:text-danger-400">
                    <div class="flex items-start gap-2">
                        <x-filament::icon icon="heroicon-m-exclamation-circle" class="mt-0.5 h-5 w-5 flex-shrink-0"/>
                        <div>
                            <p class="font-medium">{{ __('Blocking Issue') }}</p>
                            <p class="mt-1 text-xs">{{ __('This check does not meet the breeding requirements.') }}</p>
                        </div>
                    </div>
                </div>
            @elseif($check['state'] === 'check_needed')
                <div
                    class="rounded-lg bg-warning-50 p-3 text-sm text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                    <div class="flex items-start gap-2">
                        <x-filament::icon icon="heroicon-m-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0"/>
                        <div>
                            <p class="font-medium">{{ __('Review Required') }}</p>
                            <p class="mt-1 text-xs">{{ __('This check requires office review or additional information.') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </dl>
    </div>

    {{-- Additional Actions --}}
    @if(count($check['actions']) > 0)
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Available Actions') }}
            </h4>
            <div class="space-y-2">
                @foreach($check['actions'] as $action)
                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <x-filament::icon
                            :icon="$action['icon']"
                            class="h-5 w-5 text-gray-400"
                        />
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $action['label'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
