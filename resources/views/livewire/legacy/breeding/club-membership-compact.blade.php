@php
    $statusBadgeColor = match ($membershipState['status_key'] ?? null) {
        'active' => 'success',
        'expired' => 'warning',
        'not_member', 'no_user' => 'danger',
        default => 'gray',
    };

    $statusBadgeIcon = match ($membershipState['status_key'] ?? null) {
        'active' => 'heroicon-m-check-circle',
        'expired' => 'heroicon-m-clock',
        'not_member', 'no_user' => 'heroicon-m-x-circle',
        default => 'heroicon-m-question-mark-circle',
    };

    $prices = $membershipState['prices'] ?? null;
    $statusKey = $membershipState['status_key'] ?? null;

    $displayPrice = null;
    $priceLabel = __('Standard Price');

    if ($prices) {
        if ($statusKey === 'active') {
            $displayPrice = '₪' . $prices->get('member', '0');
            $priceLabel = __('Member Price');
        } else {
            $displayPrice = '₪' . $prices->get('non_member', '0');
        }
    }
@endphp

@if($membershipState)
    <div
        class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
        {{-- Status Badge --}}
        <x-filament::badge
            :color="$statusBadgeColor"
            :icon="$statusBadgeIcon"
            size="md"
        >
            {{ $membershipState['status_label'] ?? __('Unknown') }}
        </x-filament::badge>

        {{-- Price Display --}}
        @if($displayPrice)
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ $displayPrice }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $priceLabel }}
                </span>
            </div>
        @endif

        {{-- Club Name (subtle) --}}
        <div class="ml-auto text-xs text-gray-500 dark:text-gray-400">
            {{ $membershipState['club_name'] ?? '' }}
        </div>
    </div>
@else
    <div
        class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800">
        {{ __('No dog selected.') }}
    </div>
@endif
