<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="lg:col-span-1">
                <div class="max-w-4xl">
                    {{ $this->form }}
                </div>
            </div>
            <div class="lg:col-span-1">
                <x-filament::section>
                    <x-slot name="heading">{{ __('Summary') }}</x-slot>

                    <livewire:legacy.pedigree.dog-summary
                        :subjectId="$record->getKey()"
                        :key="'summary-'.$record->getKey()"/>
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
