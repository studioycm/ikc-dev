<div class="space-y-6">
    <div class="grid grid-cols-3 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Club') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $membership->club->Name }}</p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('ID') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">#{{ $membership->id }}</p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Type') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->type_label }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                @php
                    $statusLabel = match($membership->computed_status) {
                        1 => __('Active'),
                        0 => __('Inactive'),
                        2 => __('Pending Payment'),
                        3 => __('Expired'),
                        default => __('Unknown'),
                    };
                @endphp
                {{ $statusLabel }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Valid From') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->created_at?->format('Y-m-d') ?? 'N/A' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Valid Until') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->expire_date?->format('Y-m-d') ?? 'N/A' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Payment Status') }}</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                @php
                    $paymentLabel = match($membership->payment_status_code) {
                        1 => __('Paid'),
                        0 => __('Pending'),
                        null => __('N/A'),
                        default => __('Unknown'),
                    };
                @endphp
                {{ $paymentLabel }}
            </p>
        </div>

    </div>
</div>
