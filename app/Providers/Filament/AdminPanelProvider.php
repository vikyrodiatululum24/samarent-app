<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use App\Http\Middleware\EnsureAdminRole;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Width;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Facades\Filament;
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


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                \App\Filament\Admin\Resources\ReimbursementMonitorResource::class,
                \App\Filament\Absensi\Resources\ProjectResource::class,
                \App\Filament\Absensi\Resources\BranchResource::class,
                \App\Filament\Absensi\Resources\DivisionResource::class,
                \App\Filament\Absensi\Resources\SetSalaryResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->profile(EditProfile::class, false)
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
                EnsureAdminRole::class,
            ])
            ->navigationItems([
                NavigationItem::make('Admin Panel')
                    ->url('/admin', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'admin'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                        // tambahkan email lain yang diizinkan
                    ])),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->icon('heroicon-o-users')
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'user'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                        // tambahkan email lain yang diizinkan
                    ])),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'user'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                    ])),

                NavigationItem::make('Manager Panel')
                    ->url('/manager', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'manager'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                    ])),

                NavigationItem::make('Finance Panel')
                    ->url('/finance', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'finance'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                    ])),

                    NavigationItem::make('Asuransi Panel')
                    ->url('/asuransi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'asuransi'
                        && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                        ])),

                    NavigationItem::make('Admin Driver Panel')
                            ->url('/absensi', shouldOpenInNewTab: false)
                            ->group('Panels')
                            ->sort(2)
                            ->visible(fn () => Auth::check()
                                && Filament::getCurrentOrDefaultPanel()?->getId() !== 'absensi'
                                && in_array(Auth::user()?->email, [
                                'centralakun@samarent.com',
                            ])),

                    NavigationItem::make('Admin Jual Panel')
                            ->url('/penjualan', shouldOpenInNewTab: false)
                            ->group('Panels')
                            ->sort(2)
                            ->visible(fn () => Auth::check()
                                && Filament::getCurrentOrDefaultPanel()?->getId() !== 'penjualan'
                                && in_array(Auth::user()?->email, [
                                'centralakun@samarent.com',
                            ])),

                NavigationItem::make('Absensi Driver')
                    ->url('https://driver.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(3)
                    ->visible(fn () => Auth::check() && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                    ])),
                NavigationItem::make('President Panel')
                    ->url('/president', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(4)
                    ->visible(fn () => Auth::check()
                        && Filament::getCurrentOrDefaultPanel()?->getId() !== 'president'
                        && in_array(Auth::user()?->email, [
                            'centralakun@samarent.com',
                        ])),
                NavigationItem::make('Jual Unit Servicesamarent')
                    ->url('https://jualmobil.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(5)
                    ->visible(fn () => Auth::check() && in_array(Auth::user()?->email, [
                        'centralakun@samarent.com',
                    ])),
            ])
            ->databaseNotifications()
            ->maxContentWidth(Width::Full)
            ->topNavigation()
            ->viteTheme('resources/css/filament/admin/theme.css');


        // ->logoutRedirectUrl(route('admin.logout'));
    }

    public function boot(): void
    {
        Filament::registerRenderHook(
            'panels::body.end',
            fn() => view('components.footer'),
        );
    }
}
