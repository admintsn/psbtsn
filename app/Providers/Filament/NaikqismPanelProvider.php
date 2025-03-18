<?php

namespace App\Providers\Filament;

use App\Filament\Naikqism\Resources\PendaftaranNaikQismResource\Widgets\PendaftaranNaikQismTahapPertama;
use App\Filament\Naikqism\Resources\SantriResource\Widgets\DaftarkanSantriNaikQism;
use App\Filament\Naikqism\Resources\SantriResource\Widgets\FormulirUploadNaikQism;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\DashboardSantriLama;
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

class NaikqismPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('naikqism')
            ->path('naikqism')
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
            ->discoverResources(in: app_path('Filament/Naikqism/Resources'), for: 'App\\Filament\\Naikqism\\Resources')
            ->discoverPages(in: app_path('Filament/Naikqism/Pages'), for: 'App\\Filament\\Naikqism\\Pages')
            ->pages([
                DashboardSantriLama::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Naikqism/Widgets'), for: 'App\\Filament\\Naikqism\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                PendaftaranNaikQismTahapPertama::class,
                DaftarkanSantriNaikQism::class,
                FormulirUploadNaikQism::class,

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
            ])->unsavedChangesAlerts()
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
            });;
    }
}
