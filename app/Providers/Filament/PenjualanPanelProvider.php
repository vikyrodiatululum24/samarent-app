<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Enums\MaxWidth;
use App\Http\Middleware\EnsurePenjualanRole;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class PenjualanPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('penjualan')
            ->path('penjualan')
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
            ->discoverResources(in: app_path('Filament/Penjualan/Resources'), for: 'App\\Filament\\Penjualan\\Resources')
            ->discoverPages(in: app_path('Filament/Penjualan/Pages'), for: 'App\\Filament\\Penjualan\\Pages')
            ->pages([
                \App\Filament\Penjualan\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Penjualan/Widgets'), for: 'App\\Filament\\Penjualan\\Widgets')
            ->widgets([
                //
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
                EnsurePenjualanRole::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->topNavigation();
    }
}
