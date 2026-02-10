<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('IKC System')
            ->font('Assistant', provider: GoogleFontProvider::class)
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('10rem')
            ->breadcrumbs(true)
            ->favicon(url('favicon.ico'))
            ->login()
            ->profile()
            ->passwordReset()
            ->emailVerification()
            ->authGuard('web')
            // ->spa()
            ->databaseNotifications()
            // ->databaseNotificationsPolling('60s')
            ->maxContentWidth(MaxWidth::Full)
            ->colors([
                //'primary' => Color::hex('#5566aa'),
                'pink' => Color::Pink,
                'purple' => Color::Purple,
                'indigo' => Color::Indigo,
                'blue' => Color::Blue,
                'green' => Color::Green,
                'yellow' => Color::Yellow,
                'orange' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ]),
            ])
            // ->renderHook(
            //         'panels::footer',
            //         fn (): View => view('filament.components.loading-indicator')

            // )
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn(): string => __('dog/model/general.labels.navigation_group'))
                    ->icon('fas-dog'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Owners Management'))
                    ->icon('fas-user'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Shows Management'))
                    ->icon('fas-trophy'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Breedings Management'))
                    ->icon('heroicon-o-heart'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Clubs Management'))
                    ->icon('heroicon-o-flag'),
                NavigationGroup::make()
                    ->label(fn(): string => __('dog/kennel/general.labels.navigation_group'))
                    ->icon('heroicon-o-home'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Finances Management'))
                    ->icon('heroicon-o-credit-card'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Assets Management'))
                    ->icon('heroicon-o-folder'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Notifications Management'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Reports Management'))
                    ->icon('heroicon-o-presentation-chart-line'),
                NavigationGroup::make()
                    ->label(fn (): string => __('Authorisation Management'))
                    ->icon('fas-shield-dog'),
            ])
            ->navigationItems([
                NavigationItem::make('payments')
                    ->label(fn (): string => __('Payments'))
                    ->url(fn (): string => Pages\Dashboard::getUrl())
                    ->icon('heroicon-o-credit-card')
                    ->group(fn (): string => __('Finances Management'))
                    ->sort(90),

                NavigationItem::make('finance-report')
                    ->label(fn (): string => __('Finance Report'))
                    ->url(fn (): string => Pages\Dashboard::getUrl())
                    ->icon('heroicon-o-chart-bar')
                    ->group(fn (): string => __('Finances Management'))
                    ->sort(100),

                NavigationItem::make('files')
                    ->label(fn (): string => __('Files'))
                    ->url(fn (): string => Pages\Dashboard::getUrl())
                    ->icon('heroicon-o-folder')
                    ->group(fn (): string => __('Assets Management'))
                    ->sort(110),

                NavigationItem::make('messages')
                    ->label(fn (): string => __('Messages'))
                    ->url(fn (): string => Pages\Dashboard::getUrl())
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->group(fn (): string => __('Notifications Management'))
                    ->sort(120),

                NavigationItem::make('reports')
                    ->label(fn (): string => __('Reports'))
                    ->url(fn (): string => Pages\Dashboard::getUrl())
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group(fn (): string => __('Reports Management'))
                    ->sort(130),
            ])
            ->globalSearchKeyBindings(['ctrl+k', 'command+k'])
            ->globalSearchDebounce('2000')
            ->theme(asset('css/filament/admin/theme.css'))
            ->LazyLoadedDatabaseNotifications();
    }
}
