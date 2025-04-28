<div
        x-cloak
        x-show="$store.isLoading.value"
        class="fixed max-sm:bottom-4 sm:top-4 left-1/2 -translate-x-1/2 z-[6000001]"
>
    <div
            class="flex gap-2"
    >
        <div class="text-sm hidden sm:block">
            Processing
        </div>
        <x-filament::loading-indicator class="h-5 w-5"/>
    </div>
    <script>
        document.addEventListener('alpine:init', () => Alpine.store('isLoading', {
            value: false,
            delayTimer: null,
            set(value) {
                clearTimeout(this.delayTimer);
                if (value === false) this.value = false;
                else this.delayTimer = setTimeout(() => this.value = true, 2000);
            }
        }))
        document.addEventListener("livewire:init", () => {
            Livewire.hook('commit.prepare', () => Alpine.store('isLoading').set(true))
            Livewire.hook('commit', ({succeed}) => succeed(() => queueMicrotask(() => Alpine.store('isLoading').set(false))))
        })
    </script>
</div>