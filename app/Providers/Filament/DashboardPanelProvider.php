<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AssetOverviewWidget;
use App\Filament\Widgets\AttendanceChart;
use App\Filament\Widgets\AttendanceStatsOverview;
use App\Filament\Widgets\ClientRegionChart;
use App\Filament\Widgets\ProjectCalendarWidget;
use App\Filament\Widgets\ProjectStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])

            // Mengaktifkan notifikasi database bawaan Filament.
            // Ikon lonceng akan otomatis muncul di topbar.
            ->databaseNotifications()

            // (Opsional tapi direkomendasikan) Membuat notifikasi melakukan polling
            // untuk data baru setiap 30 detik tanpa perlu me-refresh halaman.
            ->databaseNotificationsPolling('30s')

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->widgets([
                AssetOverviewWidget::class,
                ProjectCalendarWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                ProjectStatsWidget::class,
                ClientRegionChart::class,
                AttendanceStatsOverview::class,
                AttendanceChart::class,
            ]);
    }
}
