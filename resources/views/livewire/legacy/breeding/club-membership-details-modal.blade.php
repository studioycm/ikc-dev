@php
    $club = $membershipState['club'] ?? null;
    $membership = $membershipState['membership'] ?? null;
    $prices = $membershipState['prices'] ?? null;

    $statusBadgeColor = match (data_get($membershipState, 'status_key')) {
        'active' => 'success',
        'expired', 'inactive', 'payment_pending' => 'warning',
        'not_member', 'no_user', 'forbidden' => 'danger',
        default => 'gray',
    };

    $statusBadgeIcon = match (data_get($membershipState, 'status_key')) {
        'active' => 'heroicon-m-check-circle',
        'expired' => 'heroicon-m-clock',
        'payment_pending' => 'heroicon-m-banknotes',
        'not_member', 'no_user', 'forbidden' => 'heroicon-m-x-circle',
        default => 'heroicon-m-question-mark-circle',
    };
@endphp

<div class="space-y-4">
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Club Information') }}
        </h4>

        <dl class="space-y-3 text-sm">
            <div class="flex items-center justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Club Name') }}</dt>
                <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                    {{ data_get($membershipState, 'club_name', '—') }}
                </dd>
            </div>

            <div class="flex items-center justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Membership Status') }}</dt>
                <dd>
                    <x-filament::badge :color="$statusBadgeColor" :icon="$statusBadgeIcon">
                        {{ data_get($membershipState, 'status_label', '—') }}
                    </x-filament::badge>
                </dd>
            </div>

            @if(data_get($club, 'address'))
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Address') }}</dt>
                    <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                        {{ data_get($club, 'address') }}
                    </dd>
                </div>
            @endif

            @if(data_get($club, 'email'))
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Email') }}</dt>
                    <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                        {{ data_get($club, 'email') }}
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    @if($membership)
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Membership Details') }}
            </h4>

            <dl class="space-y-3 text-sm">
                @if(data_get($membership, 'type_label'))
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Membership Type') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ data_get($membership, 'type_label') }}
                        </dd>
                    </div>
                @endif

                @if(data_get($membership, 'expire_date_display'))
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Valid Until') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ data_get($membership, 'expire_date_display') }}
                        </dd>
                    </div>
                @endif

                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Payment Status') }}</dt>
                    <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                        {{ data_get($membership, 'payment_status_label', '—') }}
                    </dd>
                </div>

                @if(data_get($membership, 'breeder_code'))
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Breeder Code') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ data_get($membership, 'breeder_code') }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

    @if($prices)
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Pricing') }}
            </h4>

            <dl class="space-y-3 text-sm">
                @if(data_get($prices, 'non_member.formatted'))
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Club Price') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ data_get($prices, 'non_member.formatted') }}
                        </dd>
                    </div>
                @endif

                @if(data_get($prices, 'member.formatted'))
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('Member Price') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ data_get($prices, 'member.formatted') }}
                        </dd>
                    </div>
                @endif

                <div
                    class="flex items-center justify-between gap-4 rounded-lg bg-primary-50 p-3 dark:bg-primary-500/10">
                    <dt class="font-medium text-primary-700 dark:text-primary-400">
                        {{ data_get($prices, 'final_label', __('Final Price')) }}
                    </dt>
                    <dd class="text-right text-lg font-bold text-primary-700 dark:text-primary-400">
                        {{ data_get($prices, 'final.formatted', '—') }}
                    </dd>
                </div>

                @if(data_get($prices, 'discount.amount'))
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Membership discount: :amount', ['amount' => data_get($prices, 'discount.formatted')]) }}
                    </div>
                @endif
            </dl>
        </div>
    @endif
</div>
