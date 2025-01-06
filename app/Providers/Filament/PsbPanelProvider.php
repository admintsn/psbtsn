<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Dashboardpsb;
use App\Filament\Pages\Dashboardtahapsatu;
use App\Filament\Resources\PendaftaranResource\Widgets\PendaftaranSantriBaruTahapPertama;
use App\Filament\Resources\SantriResource\Widgets\TambahCalonSantri;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Enums\ThemeMode;
use Filament\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;

class PsbPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('psb')
            ->path('psb')
            // ->login()
            ->colors([
                'danger' => "#9e5d4b",
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => "#d3c281",
                'success' => "#274043",
                'warning' => Color::Orange,
            ])
            ->font('SF Pro')
            ->brandLogo(asset('PSBTSN Logo.png'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('favicon-32x32.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                PendaftaranSantriBaruTahapPertama::class,
                TambahCalonSantri::class,
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
            ->unsavedChangesAlerts()
            ->topNavigation()
            ->breadcrumbs(false)
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Keluar'),
            ])
            ->navigation(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->bootUsing(function () {
                Notifications::alignment(Alignment::Right);
                Notifications::verticalAlignment(VerticalAlignment::End);
            });
        // ->topbar(false);
        // ->spa();
    }
}
