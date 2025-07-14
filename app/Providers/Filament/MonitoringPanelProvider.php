<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Zabbix;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use JaOcero\FilaChat\FilaChatPlugin;
use App\Filament\Widgets\HostWidgets;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tapp\FilamentWebhookClient\FilamentWebhookClientPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\ZabbixResource\Widgets\ZabbixWidget;

class MonitoringPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('monitoring')
            ->path('monitoring')
            ->login()
            // ->spa()
            ->topNavigation(true)
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
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
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Zabbix')
                    ->url(env('ZABBIX_LINK'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-link')
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('FIK')
                    ->icon('heroicon-o-bolt'),
                NavigationGroup::make()
                    ->label('GKB')
                    ->icon('heroicon-o-bolt'),
                NavigationGroup::make()
                    ->label('Report')
                    ->icon('heroicon-o-document-duplicate'),
            ])
            ->plugins([
                FilamentWebhookClientPlugin::make(),
                \pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin::make(),
                \TomatoPHP\FilamentLogger\FilamentLoggerPlugin::make(),
                \FilamentWebpush\FilamentWebpushPlugin::make()
                    ->registerSubscriptionStatsWidget(true),
                \ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin::make()
                    ->authorize(fn(): bool => auth()->user()->email === 'admin@gmail.com'),
                // \TomatoPHP\FilamentPWA\FilamentPWAPlugin::make()
                // FilaChatPlugin::make()
            ])
            ->brandLogo(asset('images/logo_tik.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/logo_tik.png'));
        // ->viteTheme('resources/css/filament/monitoring/theme.css');
    }
}
