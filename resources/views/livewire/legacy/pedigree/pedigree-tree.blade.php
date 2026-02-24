<div class="space-y-6">

    @unless($loaded)
        <div class="flex justify-center">
            {{ $this->loadPedigreeAction }}
        </div>
    @else

        {{-- MAIN DOG SECTION --}}
        <div class="col-span-full">
            @include('legacy.pedigree.pedigree-main-dog', [
                'dog' => $dog
            ])
        </div>

        {{-- ANCESTOR GRID --}}
        <div class="space-y-8">

            @foreach($this->generations as $generation => $dogs)
                @continue($generation === 0)

                <div
                    class="grid gap-4"
                    style="grid-template-columns: repeat({{ count($dogs) }}, minmax(0,1fr));"
                >
                    @foreach($dogs as $ancestor)
                        @include('legacy.pedigree.pedigree-node', [
                            'dog' => $ancestor
                        ])
                    @endforeach
                </div>
            @endforeach

        </div>

    @endunless

</div>
