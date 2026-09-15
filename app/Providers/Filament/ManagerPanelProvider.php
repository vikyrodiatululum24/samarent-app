<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Support\Enums\Width;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Auth\EditProfile;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class ManagerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('manager')
            ->path('manager')
            ->brandName('SAMARENT')
            ->favicon(asset('images/icon.png'))
            ->brandLogo(asset('images/Samarent.png')) // ganti logo
            ->brandLogoHeight('50px')
            ->login(false)
            ->colors([
                'primary' => '#4F46E5', // ganti warna utama
                'secondary' => '#b5b4cc', // ganti warna sekunder
                'danger' => '#EF4444', // ganti warna bahaya
                'brown' => '#A16207', // ganti warna coklat
                'success' => '#22C55E', // ganti warna sukses
                'yellow' => '#FBBF24', // ganti warna peringatan
            ])
            ->discoverResources(in: app_path('Filament/Manager/Resources'), for: 'App\\Filament\\Manager\\Resources')            ->resources([
                \App\Filament\Resources\ReimbursementResource::class,
            ])            ->discoverPages(in: app_path('Filament/Manager/Pages'), for: 'App\\Filament\\Manager\\Pages')
            ->profile(EditProfile::class, false)
            ->pages([
                // Dashboard hidden
            ])
            ->discoverWidgets(in: app_path('Filament/Manager/Widgets'), for: 'App\\Filament\\Manager\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                \App\Http\Middleware\EnsureManagerRole::class,
            ])
            ->navigationItems([
                NavigationItem::make('Dashboard')
                    ->url('/manager', shouldOpenInNewTab: false)
                    ->sort(0),
                NavigationItem::make('Admin Panel')
                    ->url('/admin', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'admin'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'user'
                        && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Finance Panel')
                    ->url('/finance', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(3)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'finance'
                        && auth()->user()?->isSuperAdmin()),

                    NavigationItem::make('Asuransi Panel')
                    ->url('/asuransi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(4)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'asuransi'
                        && auth()->user()?->isSuperAdmin()),

                    NavigationItem::make('Admin Driver Panel')
                            ->url('/absensi', shouldOpenInNewTab: false)
                            ->group('Panels')
                            ->sort(5)
                            ->visible(fn () => auth()->check()
                                && Filament::getCurrentPanel()?->getId() !== 'absensi'
                                && auth()->user()?->isSuperAdmin()),

                    NavigationItem::make('Admin Jual Panel')
                            ->url('/penjualan', shouldOpenInNewTab: false)
                            ->group('Panels')
                            ->sort(6)
                            ->visible(fn () => auth()->check()
                                && Filament::getCurrentPanel()?->getId() !== 'penjualan'
                                && auth()->user()?->isSuperAdmin()),

                NavigationItem::make('Absensi Driver')
                    ->url('https://driver.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(7)
                    ->visible(fn () => auth()->check() && auth()->user()?->isSuperAdmin()),
                NavigationItem::make('President Panel')
                    ->url('/president', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(8)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'president'
                        && auth()->user()?->isSuperAdmin()),
                NavigationItem::make('Jual Unit Servicesamarent')
                    ->url('https://jualmobil.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(9)
                    ->visible(fn () => auth()->check() && auth()->user()?->isSuperAdmin()),
            ])
            ->maxContentWidth(Width::Full)
            ->topNavigation()
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
