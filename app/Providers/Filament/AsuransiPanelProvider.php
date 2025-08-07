<?php

namespace App\Providers\Filament;

use App\Filament\Asuransi\Resources\AsuransiResource;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
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
            ->discoverResources(in: app_path('Filament/Asuransi/Resources'), for: 'App\\Filament\\Asuransi\\Resources')
            ->discoverPages(in: app_path('Filament/Asuransi/Pages'), for: 'App\\Filament\\Asuransi\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->resources([
                AsuransiResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Asuransi/Widgets'), for: 'App\\Filament\\Asuransi\\Widgets')
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
            ->databaseNotifications()
            ->maxContentWidth(MaxWidth::Full)
            ->topNavigation()
            ->authMiddleware([
                \App\Http\Middleware\EnsureAsuransiRole::class,
            ]);
    }
}
