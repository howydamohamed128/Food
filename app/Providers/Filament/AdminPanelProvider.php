<?php

namespace App\Providers\Filament;

use App\Filament\Pages\LoginPage;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Models\Role;
use App\Settings\GeneralSettings;

class AdminPanelProvider extends PanelProvider {
    public function panel(Panel $panel): Panel {

        return $panel
            ->default()
            ->font('cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@700;800&display=swap')
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogoHeight("2rem")
//            ->spa()
            ->passwordReset()
            ->userMenuItems([
                'profile' => MenuItem::make()->label(fn() => __('menu.edit_profile'))->url(fn() => route('filament.admin.profile')),
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([

                NavigationGroup::make()->label(fn(): string => __('menu.banners')),
                // NavigationGroup::make()->label(fn(): string => __('menu.users_activities')),
                NavigationGroup::make()->label(fn(): string => __('menu.categories')),
                NavigationGroup::make()->label(fn(): string => __('menu.products')),
                // NavigationGroup::make()->label(fn(): string => __('menu.reports')),
                NavigationGroup::make()->label(fn(): string => __('menu.content')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.locations')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.notifications')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.settings')),

            ])
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->brandLogoHeight('2.5rem')
             ->brandLogo(function (GeneralSettings $settings) {
                return $settings->app_logo &&  Storage::disk('public')->exists($settings->app_logo)  ? asset('storage/' . $settings->app_logo) : asset('logo.jpg');
            })
            ->favicon(function (GeneralSettings $settings) {
                return $settings->app_logo &&  Storage::disk('public')->exists($settings->app_logo)  ? asset('storage/' . $settings->app_logo) : asset('logo.jpg');
            })
            ->brandName('Istanbul Food')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()->defaultLocales(['en', 'ar']),

                ThemesPlugin::make()
                    ->canViewThemesPage(fn() => auth()?->user()?->email === 'ahmed.mostafa.dev.eg@gmail.com'),

            ])
            ->spa()
            ->userMenuItems([
            ])
            // ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->navigationItems([
                // NavigationItem::make("roles")
                //     ->label(fn(): string => __('filament-shield::filament-shield.nav.group'))
                //     ->url('/admin/shield/roles')
                //     ->icon('heroicon-o-users')
                //     ->group(fn() => __('menu.settings'))
                //     ->hidden(fn() => !auth()->user()->can('viewAny', Role::class))
                //     ->sort(5),
            ])
            ->resources([
//                config('filament-logger.activity_resource')

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
                SetTheme::class,
//                SetLocale::class

            ])
            ->darkMode(false)
            ->authMiddleware([

                Authenticate::class,
            ]);
    }

}
