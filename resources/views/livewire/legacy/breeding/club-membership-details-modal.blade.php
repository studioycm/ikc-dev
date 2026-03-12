<div class="space-y-4">
    {{-- Club Information --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Club Information') }}
        </h4>
        <dl class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Club Name') }}</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $membershipState['club_name'] ?? '—' }}
                </dd>
            </div>

            <div class="flex items-center justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('Membership Status') }}</dt>
                <dd>
                    <x-filament::badge
                        :color="match($membershipState['status_key'] ?? null) {
                            'active' => 'success',
                            'expired' => 'warning',
                            'not_member', 'no_user' => 'danger',
                            default => 'gray',
                        }"
                        :icon="match($membershipState['status_key'] ?? null) {
                            'active' => 'heroicon-m-check-circle',
                            'expired' => 'heroicon-m-clock',
                            'not_member', 'no_user' => 'heroicon-m-x-circle',
                            default => 'heroicon-m-question-mark-circle',
                        }"
                    >
                        {{ $membershipState['status_label'] ?? '—' }}
                    </x-filament::badge>
                </dd>
            </div>

            @if(isset($membershipState['membership']) && $membershipState['membership'])
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Valid Until') }}</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $membershipState['membership']->expire_date ? $membershipState['membership']->expire_date->format('d/m/Y') : '—' }}
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Pricing Information --}}
    @if(isset($membershipState['prices']) && $membershipState['prices'])
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Pricing') }}
            </h4>
            <dl class="space-y-3 text-sm">
                @if($membershipState['status_key'] === 'active')
                    <div class="flex items-center justify-between rounded-lg bg-success-50 p-3 dark:bg-success-500/10">
                        <dt class="font-medium text-success-700 dark:text-success-400">
                            {{ __('Member Price') }}
                        </dt>
                        <dd class="text-lg font-bold text-success-700 dark:text-success-400">
                            ₪{{ $membershipState['prices']->get('member', '0') }}
                        </dd>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <x-filament::icon icon="heroicon-m-information-circle" class="h-4 w-4"/>
                        <span>{{ __('Discounted rate for active members') }}</span>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">
                            {{ __('Standard Price') }}
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            ₪{{ $membershipState['prices']->get('non_member', '0') }}
                        </dd>
                    </div>
                @endif

                <div class="flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400">
                        {{ __('Per Puppy') }}
                    </dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">
                        ₪{{ $membershipState['prices']->get($membershipState['status_key'] === 'active' ? 'member' : 'non_member', '0') }}
                    </dd>
                </div>
            </dl>
        </div>
    @endif

    {{-- Club Breeding Conditions --}}
    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <h4 class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Club Breeding Conditions') }}
        </h4>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Club special breeding conditions will be displayed here.') }}
        </p>
    </div>
</div>
