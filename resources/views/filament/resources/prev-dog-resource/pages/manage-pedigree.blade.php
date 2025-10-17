<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="md:col-span-1">
                <div class="">
                    {{ $this->form }}
                </div>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <x-filament::section>
                    <x-slot name="heading">{{ __('Dog Summary') }}</x-slot>

                    @if($record?->exists)
                        <livewire:legacy.pedigree.dog-summary
                            :subjectId="$record->getKey()"
                            :key="'summary-'.$record->getKey()"/>
                    @else
                        <div class="text-sm text-gray-500">
                            {{ __('Select a dog to view its summary') }}
                        </div>
                    @endif
                </x-filament::section>
            </div>
        </div>
        @if($record?->exists)

            <div class="">
                <x-filament::section>
                    <x-slot name="heading">{{ __('Pedigree Builder') }}</x-slot>

                    <livewire:legacy.pedigree.parents-pair-form
                        :subjectId="$record->getKey()"
                        :depth="1"
                        :maxDepth="4"
                        :expanded="false"
                        :key="'root-'.$record->getKey()"/>

                    <x-filament-actions::modals/>
                </x-filament::section>
            </div>

        @endif
    </div>
</x-filament-panels::page>
