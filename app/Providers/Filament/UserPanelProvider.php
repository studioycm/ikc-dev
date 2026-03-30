<?php

namespace App\Providers\Filament;

use App\Filament\User\Pages\BreedingActivityDashboard;
use App\Filament\User\Pages\Dashboard as UserDashboard;
use App\Filament\User\Pages\DogsDashboard;
use App\Filament\User\Pages\MembershipsDashboard;
use App\Filament\User\Pages\PaymentsDashboard;
use App\Filament\User\Pages\RequestsDashboard;
use App\Filament\User\Pages\ShowsDashboard;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->profile()
            ->passwordReset()
            ->authGuard('web')
            ->databaseNotifications()
            ->maxContentWidth(MaxWidth::Full)
            ->breadcrumbs(true)
            ->favicon(url('favicon.ico'))
            ->font('Assistant', provider: GoogleFontProvider::class)
            ->topNavigation()
            ->colors([
                'primary' => Color::Amber,
                'pink' => Color::Pink,
                'purple' => Color::Purple,
                'indigo' => Color::Indigo,
                'blue' => Color::Blue,
                'green' => Color::Green,
                'yellow' => Color::Yellow,
                'orange' => Color::Orange,
            ])
            ->theme(asset('css/filament/admin/theme.css'))
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                UserDashboard::class,
                DogsDashboard::class,
                MembershipsDashboard::class,
                RequestsDashboard::class,
                PaymentsDashboard::class,
                BreedingActivityDashboard::class,
                ShowsDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_END, function () {
                return Blade::render('filament.user.components.prev-user-badge',
                    [
                        'color' => 'primary',
                        'icon' => 'fas-user',
                        'prev_user_name' => auth()->user()->prevUser->name,
                        'prev_user_id' => auth()->user()->prevUser->id,
                        'prev_user_phone' => auth()->user()->prevUser->normalised_phone,
                    ]);
            }
            );
    }
}
