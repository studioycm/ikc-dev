<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Club Committee & Contacts') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Contact information for club management and breed promoters') }}
        </x-slot>

        <div class="space-y-8">
            @php
                $clubsData = $this->getClubStaffData();
            @endphp

            @forelse($clubsData as $clubID => $clubData)
                <div class="space-y-4">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $clubData['name'] }}
                        </h3>
                        <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
                            @if(filled($clubData['email'] ?? null))
                                <a href="mailto:{{ $clubData['email'] }}"
                                   class="flex items-center gap-2 hover:underline">
                                    <x-heroicon-o-envelope class="h-4 w-4"/>
                                    {{ $clubData['email'] }}
                                </a>
                            @endif

                            @if(filled($clubData['address'] ?? null))
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-map-pin class="h-4 w-4"/>
                                    {{ $clubData['address'] }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Breeds') }}</h3>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($clubData['breeds'] ?? [] as $breed)
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ $breed }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Club Staff') }}</h4>
                            <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-{{ $this->getGridColumns() }}">
                                @forelse($clubData['staff'] as $staffMember)
                                    <div
                                        class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $staffMember['name'] ?? __('Unknown') }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($staffMember['titles'] ?? [] as $title)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $title }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <div class="mt-3 space-y-1">
                                            @if(filled($staffMember['email'] ?? null))
                                                <a href="mailto:{{ $staffMember['email'] }}"
                                                   class="flex items-center gap-2 text-xs text-blue-600 hover:underline dark:text-blue-400">
                                                    <x-heroicon-o-envelope class="h-3 w-3"/>
                                                    {{ $staffMember['email'] }}
                                                </a>
                                            @endif

                                            @if(filled($staffMember['mobile_phone'] ?? null))
                                                <a href="tel:{{ $staffMember['mobile_phone'] }}"
                                                   class="flex items-center gap-2 text-xs text-blue-600 hover:underline dark:text-blue-400">
                                                    <x-heroicon-o-phone class="h-3 w-3"/>
                                                    {{ $staffMember['mobile_phone'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ __('No club staff found.') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Promoters') }}</h4>
                            <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-{{ $this->getGridColumns() }}">
                                @forelse($clubData['promoters'] as $promoter)
                                    <div
                                        class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $promoter['name'] ?? __('Unknown') }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($promoter['titles'] ?? [] as $title)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    {{ $title }}
                                                </span>
                                            @endforeach
                                        </div>

                                        @if(!empty($promoter['breeds']))
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach($promoter['breeds'] as $breed)
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                                        {{ $breed }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="mt-3 space-y-1">
                                            @if(filled($promoter['email'] ?? null))
                                                <a href="mailto:{{ $promoter['email'] }}"
                                                   class="flex items-center gap-2 text-xs text-blue-600 hover:underline dark:text-blue-400">
                                                    <x-heroicon-o-envelope class="h-3 w-3"/>
                                                    {{ $promoter['email'] }}
                                                </a>
                                            @endif

                                            @if(filled($promoter['mobile_phone'] ?? null))
                                                <a href="tel:{{ $promoter['mobile_phone'] }}"
                                                   class="flex items-center gap-2 text-xs text-blue-600 hover:underline dark:text-blue-400">
                                                    <x-heroicon-o-phone class="h-3 w-3"/>
                                                    {{ $promoter['mobile_phone'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ __('No promoters found.') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400"/>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{__('No Club Staff Found')}}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{__('Club management information will appear here once available')}}.
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
