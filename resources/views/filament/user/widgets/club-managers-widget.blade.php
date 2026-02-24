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
                $clubsData = $this->getManagersByRole();
            @endphp

            @forelse($clubsData as $clubID => $clubData)
                <div class="space-y-4">
                    {{-- Club Header --}}
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $clubData['name'] }}
                        </h3>
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

                    {{-- Manager Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-{{ $this->grid_columns }} gap-4">
                        {{-- Chairman --}}
                        @foreach($clubData['managers']['chairman'] as $manager)
                            <div
                                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $manager->full_name ?? __('Unknown') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900 px-2 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-200">
                                                {{__('Chairman')}}
                                            </span>
                                        </p>

                                        {{-- Contact Info --}}
                                        <div class="mt-2 space-y-1">
                                            @if($manager->email)
                                                <a href="mailto:{{ $manager->email }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-envelope class="w-3 h-3 mr-1"/>
                                                    {{ $manager->email }}
                                                </a>
                                            @endif

                                            @if($manager->mobile_phone)
                                                <a href="tel:{{ $manager->mobile_phone }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-phone class="w-3 h-3 mr-1"/>
                                                    {{ $manager->mobile_phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Secretary --}}
                        @foreach($clubData['managers']['secretary'] as $manager)
                            <div
                                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $manager->full_name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900 px-2 py-0.5 text-xs font-medium text-green-800 dark:text-green-200">
                                                {{__('Secretary')}}
                                            </span>
                                        </p>

                                        <div class="mt-2 space-y-1">
                                            @if($manager->email)
                                                <a href="mailto:{{ $manager->email }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-envelope class="w-3 h-3 mr-1"/>
                                                    {{ $manager->email }}
                                                </a>
                                            @endif

                                            @if($manager->mobile_phone)
                                                <a href="tel:{{ $manager->mobile_phone }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-phone class="w-3 h-3 mr-1"/>
                                                    {{ $manager->mobile_phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Accountant --}}
                        @foreach($clubData['managers']['accountant'] as $manager)
                            <div
                                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $manager->full_name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900 px-2 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-200">
                                                {{__('Accountant')}}
                                            </span>
                                        </p>

                                        <div class="mt-2 space-y-1">
                                            @if($manager->email)
                                                <a href="mailto:{{ $manager->email }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-envelope class="w-3 h-3 mr-1"/>
                                                    {{ $manager->email }}
                                                </a>
                                            @endif

                                            @if($manager->mobile_phone)
                                                <a href="tel:{{ $manager->mobile_phone }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-phone class="w-3 h-3 mr-1"/>
                                                    {{ $manager->mobile_phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Breed Promoters --}}
                        @foreach($clubData['managers']['promoters'] as $manager)
                            <div
                                class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $manager->full_name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <span
                                                class="inline-flex items-center rounded-full bg-purple-100 dark:bg-purple-900 px-2 py-0.5 text-xs font-medium text-purple-800 dark:text-purple-200">
                                                {{__('Breed Promoter')}}
                                            </span>
                                        </p>

                                        <div class="mt-2 space-y-1">
                                            @if($manager->email)
                                                <a href="mailto:{{ $manager->email }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-envelope class="w-3 h-3 mr-1"/>
                                                    {{ $manager->email }}
                                                </a>
                                            @endif

                                            @if($manager->mobile_phone)
                                                <a href="tel:{{ $manager->mobile_phone }}"
                                                   class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2">
                                                    <x-heroicon-o-phone class="w-3 h-3 mr-1"/>
                                                    {{ $manager->mobile_phone }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
