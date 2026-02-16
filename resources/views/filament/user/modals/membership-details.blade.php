<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Club Name</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $membership->club->Name }}</p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Number</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">#{{ $membership->id }}</p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Membership Type</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->type_label }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                @php
                    $statusLabel = match($membership->computed_status) {
                        1 => 'Active',
                        0 => 'Inactive',
                        2 => 'Pending Payment',
                        3 => 'Expired',
                        default => 'Unknown',
                    };
                @endphp
                {{ $statusLabel }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Valid From</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->created_at?->format('Y-m-d') ?? 'N/A' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Valid Until</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                {{ $membership->expire_date?->format('Y-m-d') ?? 'N/A' }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</h3>
            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                @php
                    $paymentLabel = match($membership->payment_status_code) {
                        1 => 'Paid',
                        0 => 'Pending',
                        null => 'N/A',
                        default => 'Unknown',
                    };
                @endphp
                {{ $paymentLabel }}
            </p>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Associated Breeds</h3>
            <div class="mt-1 flex flex-wrap gap-1">
                @foreach($membership->club->breeds ?? [] as $breed)
                    <span
                        class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ $breed->BreedName }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
