<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Navigation\NavigationItem;
use App\Http\Middleware\EnsureAbsensiRole;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AbsensiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('absensi')
            ->path('absensi')
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
            ->discoverResources(in: app_path('Filament/Absensi/Resources'), for: 'App\\Filament\\Absensi\\Resources')
            ->discoverPages(in: app_path('Filament/Absensi/Pages'), for: 'App\\Filament\\Absensi\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Absensi/Widgets'), for: 'App\\Filament\\Absensi\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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

            ->navigationItems([
                NavigationItem::make('Admin Panel')
                    ->url('/admin', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'admin'
                        && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                        // tambahkan email lain yang diizinkan
                    ])),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'user'
                        && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),

                NavigationItem::make('Manager Panel')
                    ->url('/manager', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(3)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'manager'
                        && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),

                NavigationItem::make('Finance Panel')
                    ->url('/finance', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(4)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'finance'
                        && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),

                    NavigationItem::make('Asuransi Panel')
                    ->url('/asuransi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(5)
                    ->visible(fn () => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'asuransi'
                        && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                        ])),

                    NavigationItem::make('Admin Jual Panel')
                            ->url('/penjualan', shouldOpenInNewTab: false)
                            ->group('Panels')
                            ->sort(6)
                            ->visible(fn () => auth()->check()
                                && Filament::getCurrentPanel()?->getId() !== 'penjualan'
                                && in_array(auth()->user()->email, [
                                'centralakun@samarent.com',
                            ])),

                NavigationItem::make('Absensi Driver')
                    ->url('https://driver.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('External Links')
                    ->sort(7)
                    ->visible(fn () => auth()->check() && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),
                NavigationItem::make('Jual Unit Servicesamarent')
                    ->url('https://jualmobil.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('External Links')
                    ->sort(8)
                    ->visible(fn () => auth()->check() && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),
            ])
            ->databaseNotifications()
            ->maxContentWidth(MaxWidth::Full)
            ->topNavigation()
            ->authMiddleware([
                EnsureAbsensiRole::class,
            ]);
    }
}
