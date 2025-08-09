<!-- resources/views/filament/resources/prev-club/view.blade.php -->
@php
    /** @var \Filament\Infolists\Infolist $infolist */
@endphp

<x-filament::page>
    <div class="space-y-6">
        <div>
            {!! $infolist->render() !!}
        </div>
    </div>
</x-filament::page>
