<div>
    @php
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

        $clubPrice = data_get($membershipState, 'prices.non_member.formatted');
        $finalPrice = data_get($membershipState, 'prices.final.formatted');
        $finalPriceLabel = data_get($membershipState, 'prices.final_label', __('Final Price'));
        $membershipType = data_get($membershipState, 'membership.type_label');
        $validUntil = data_get($membershipState, 'membership.expire_date_display');
    @endphp

    @if($membershipState)
        <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-filament::badge
                            :color="$statusBadgeColor"
                            :icon="$statusBadgeIcon"
                            size="md"
                        >
                            {{ data_get($membershipState, 'status_label', __('Unknown')) }}
                        </x-filament::badge>

                        @if($membershipType)
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $membershipType }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-950 dark:text-white">
                            {{ data_get($membershipState, 'club_name', __('Unknown club')) }}
                        </div>

                        @if($validUntil)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Valid until :date', ['date' => $validUntil]) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid gap-2 text-sm lg:min-w-56 lg:text-right">
                    @if($clubPrice)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Club Price') }}</div>
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $clubPrice }}</div>
                        </div>
                    @endif

                    @if($finalPrice)
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $finalPriceLabel }}</div>
                            <div class="text-base font-semibold text-gray-950 dark:text-white">{{ $finalPrice }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                {{ $this->viewDetailsAction }}
            </div>
        </div>
    @else
        <div
            class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800">
            {{ __('No dog selected.') }}
        </div>
    @endif

    <x-filament-actions::modals/>
</div>
