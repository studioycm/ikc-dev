<?php

namespace App\Providers;

use App\Models\PrevBreed;
use App\Models\PrevDog;
use App\Observers\PrevBreedObserver;
use App\Observers\PrevDogObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['he', 'en']); // also accepts a closure
        });

        // Register observers that clear club counts cache when dogs/breeds change.
        PrevDog::observe(PrevDogObserver::class);
        PrevBreed::observe(PrevBreedObserver::class);

        // Register a custom render hook to add a script for scrolling to the topof table (e.g. after pagination change)
        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): string => new HtmlString('
        <script>document.addEventListener("scroll-to-top", () => window.scrollTo(0, 0))</script>
            '),
        );
    }
}
