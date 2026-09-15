<?php

namespace App\Providers\Filament;

use App\Filament\Asuransi\Resources\AsuransiResource;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\EventHolidayListWidget;
use Filament\Facades\Filament;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AsuransiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->home(AsuransiResource::class)
            ->id('asuransi')
            ->path('asuransi')
            ->favicon(asset('images/icon.png'))
            ->brandName('SAMARENT')
            ->brandLogo(asset('images/Samarent.png')) // ganti logo
            ->brandLogoHeight('50px')
            ->login(false)
            ->colors([
                'primary' => '#4F46E5', // ganti warna utama
                'secondary' => '#b5b4cc', // ganti warna sekunder
                'brown' => '#A16207', // ganti warna coklat
                'danger' => '#EF4444', // ganti warna bahaya
                'success' => '#22C55E', // ganti warna sukses
                'yellow' => '#FBBF24', // ganti warna peringatan
            ])
            ->discoverResources(in: app_path('Filament/Asuransi/Resources'), for: 'App\\Filament\\Asuransi\\Resources')->resources([
                \App\Filament\Resources\ReimbursementResource::class,
            ])->discoverPages(in: app_path('Filament/Asuransi/Pages'), for: 'App\\Filament\\Asuransi\\Pages')
            ->profile(EditProfile::class, false)
            ->pages([
                \App\Filament\Asuransi\Pages\Dashboard::class,
            ])
            ->resources([
                AsuransiResource::class,
                \App\Filament\Resources\BengkelResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Asuransi/Widgets'), for: 'App\\Filament\\Asuransi\\Widgets')
            ->widgets([
                // CalendarWidget::class,
                // EventHolidayListWidget::class,
            ])
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
            )
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
            ->navigationItems([
                NavigationItem::make('Dashboard')
                    ->url('/asuransi', shouldOpenInNewTab: false)
                    ->sort(0),

                NavigationItem::make('Admin Panel')
                    ->url('/admin', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'admin'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'user'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Manager Panel')
                    ->url('/manager', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(3)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'manager'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Finance Panel')
                    ->url('/finance', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(4)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'finance'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Admin Driver Panel')
                    ->url('/absensi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(5)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'absensi'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Admin Jual Panel')
                    ->url('/penjualan', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(6)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'penjualan'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Absensi Driver')
                    ->url('https://driver.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(7)
                    ->visible(fn() => auth()->check() && auth()->user()?->isSuperAdmin()),
                NavigationItem::make('President Panel')
                    ->url('/president', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(8)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'president'
                        && auth()->user()?->isSuperAdmin()),
                NavigationItem::make('Jual Unit Servicesamarent')
                    ->url('https://jualmobil.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(9)
                    ->visible(fn() => auth()->check() && auth()->user()?->isSuperAdmin()),
            ])
            ->databaseNotifications()
            ->maxContentWidth(Width::Full)
            ->topNavigation()
            ->authMiddleware([
                \App\Http\Middleware\EnsureAsuransiRole::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
