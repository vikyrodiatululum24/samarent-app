<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\DetailHistori;
use App\Filament\Pages\HistoriPengajuan;
use App\Filament\Pages\Mount;
use App\Filament\Pages\SettingsPage;
use App\Filament\Resources\Bbms\BbmResource;
use App\Filament\Resources\BengkelResource;
use App\Filament\Resources\DataUnitResource;
use App\Filament\Resources\FormTugasResource;
use App\Filament\Resources\LaporanKeuanganServiceResource;
use App\Filament\Resources\NorekResource;
use App\Filament\Resources\PenggunaResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ReimbursementResource;
use App\Filament\Resources\UnitJualResource;
use App\Http\Middleware\EnsurePresidentRole;
use Filament\Facades\Filament;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class PresidentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('president')
            ->path('president')
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
            ->discoverResources(in: app_path('Filament/President/Resources'), for: 'App\\Filament\\President\\Resources')
            ->resources([
                DataUnitResource::class,
                BbmResource::class,
                BengkelResource::class,
                NorekResource::class,
                ProjectResource::class,
                ReimbursementResource::class,
                UnitJualResource::class,
                FormTugasResource::class,
                PenggunaResource::class,
            ])
            ->discoverPages(in: app_path('Filament/President/Pages'), for: 'App\\Filament\\President\\Pages')
            ->pages([
                Dashboard::class,
                SettingsPage::class,
                DetailHistori::class,
                HistoriPengajuan::class,
                Mount::class,
            ])
            ->discoverWidgets(in: app_path('Filament/President/Widgets'), for: 'App\\Filament\\President\\Widgets')
            ->widgets([
                // Tambahkan widget khusus untuk panel President di sini
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
            ->authMiddleware([
                EnsurePresidentRole::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Pengajuan'),
                NavigationGroup::make()
                    ->label('Panels'),
                NavigationGroup::make()
                    ->label('Pengaturan'),
                NavigationGroup::make()
                    ->label('Unit'),
                NavigationGroup::make()
                    ->label('Master Data'),
                NavigationGroup::make()
                    ->label('Keuangan'),
            ])
            ->navigationItems([
                NavigationItem::make('Dashboard')
                    ->url('/president', shouldOpenInNewTab: false)
                    ->sort(0),
                NavigationItem::make('Admin Panel')
                    ->url('/admin', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(1)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'admin'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                            // tambahkan email lain yang diizinkan
                        ])),

                NavigationItem::make('User Panel')
                    ->url('/user', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(2)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'user'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),

                NavigationItem::make('Manager Panel')
                    ->url('/manager', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(3)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'manager'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),

                NavigationItem::make('Finance Panel')
                    ->url('/finance', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(4)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'finance'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),

                NavigationItem::make('Asuransi Panel')
                    ->url('/asuransi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(5)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'asuransi'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),

                NavigationItem::make('Admin Driver Panel')
                    ->url('/absensi', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(6)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'absensi'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),
                NavigationItem::make('Admin Jual Panel')
                    ->url('/penjualan', shouldOpenInNewTab: false)
                    ->group('Panels')
                    ->sort(6)
                    ->visible(fn() => auth()->check()
                        && Filament::getCurrentPanel()?->getId() !== 'penjualan'
                        && in_array(auth()->user()->email, [
                            'centralakun@samarent.com',
                        ])),

                NavigationItem::make('Absensi Driver')
                    ->url('https://driver.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(7)
                    ->visible(fn() => auth()->check() && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),
                NavigationItem::make('Jual Unit Servicesamarent')
                    ->url('https://jualmobil.servicesamarent.com', shouldOpenInNewTab: true)
                    ->group('Panels')
                    ->sort(8)
                    ->visible(fn() => auth()->check() && in_array(auth()->user()->email, [
                        'centralakun@samarent.com',
                    ])),
            ])
            ->maxContentWidth(Width::Full)
            ->topNavigation()
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
