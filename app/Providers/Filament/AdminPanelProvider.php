<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Sistema de Locação')
            ->brandLogo(new HtmlString(<<<'HTML'
                <span class="flux-sidebar-brand">
                    <img src="/images/logo-locacao.svg" alt="Logo Sistema de Locação">
                    <strong>Sistema de Locação</strong>
                </span>
            HTML))
            ->darkModeBrandLogo(new HtmlString(<<<'HTML'
                <span class="flux-sidebar-brand">
                    <img src="/images/logo-locacao.svg" alt="Logo Sistema de Locação">
                    <strong>Sistema de Locação</strong>
                </span>
            HTML))
            ->brandLogoHeight('2.4rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Fuchsia,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <style>
                        .fi-body {
                            background:
                                radial-gradient(circle at top, rgba(99, 102, 241, 0.24), transparent 28%),
                                radial-gradient(circle at 85% 15%, rgba(45, 212, 191, 0.16), transparent 22%),
                                linear-gradient(180deg, #07111f 0%, #091525 44%, #0a1320 100%);
                            color: #e5eefb;
                        }

                        .fi-layout {
                            position: relative;
                        }

                        .fi-sidebar {
                            padding: 1.25rem 0 1.25rem 1.25rem;
                            background: transparent !important;
                        }

                        .fi-sidebar-header,
                        .fi-sidebar-nav,
                        .fi-topbar nav,
                        .fi-section,
                        .fi-ta-ctn,
                        .fi-wi-stats-overview-stat,
                        .fi-simple-page,
                        .fi-dropdown-panel,
                        .fi-modal-window {
                            border: 1px solid rgba(162, 0, 255, 0.46) !important;
                            background:
                                linear-gradient(180deg, rgba(15, 23, 42, 0.92), rgba(8, 15, 28, 0.84)) !important;
                            box-shadow:
                                0 24px 70px rgba(2, 6, 23, 0.42),
                                0 0 0 1px rgba(217, 112, 255, 0.12),
                                inset 0 1px 0 rgba(255, 255, 255, 0.04);
                            backdrop-filter: blur(22px);
                        }

                        .fi-sidebar-header {
                            margin-right: 1.25rem;
                            border-radius: 1.75rem 1.75rem 0 0;
                            min-height: 5rem;
                        }

                        .fi-sidebar-nav {
                            margin-right: 1.25rem;
                            border-radius: 0 0 1.75rem 1.75rem;
                            padding-top: 1.25rem;
                            padding-bottom: 1.5rem;
                        }

                        .fi-topbar {
                            padding-top: 1.25rem;
                        }

                        .fi-topbar nav {
                            min-height: 4.75rem;
                            margin-inline: 1.25rem;
                            border-radius: 1.75rem;
                            padding-inline: 1.5rem;
                        }

                        .flux-sidebar-brand {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.55rem;
                            line-height: 1;
                        }

                        .flux-sidebar-brand img {
                            width: 2.2rem;
                            height: 2.2rem;
                            object-fit: contain;
                        }

                        .flux-sidebar-brand strong {
                            color: #f8fbff;
                            font-size: 0.95rem;
                            font-weight: 700;
                            white-space: nowrap;
                        }

                        .fi-user-menu-trigger .fi-avatar,
                        .fi-user-avatar,
                        .fi-avatar {
                            display: none !important;
                        }

                        .fi-main {
                            max-width: 100% !important;
                            padding-bottom: 2rem;
                        }

                        .fi-page > section {
                            gap: 1.75rem;
                            padding-top: 1.5rem;
                            padding-bottom: 1.75rem;
                        }

                        .fi-simple-page {
                            position: relative;
                            overflow: hidden;
                            border-radius: 2rem;
                            padding: 2rem;
                        }

                        .fi-simple-page::before {
                            content: '';
                            position: absolute;
                            inset: 0;
                            background:
                                radial-gradient(circle at top left, rgba(99, 102, 241, 0.16), transparent 32%),
                                radial-gradient(circle at bottom right, rgba(45, 212, 191, 0.12), transparent 28%);
                            pointer-events: none;
                        }

                        .fi-simple-page > section,
                        .fi-page,
                        .fi-section,
                        .fi-ta-ctn,
                        .fi-wi-stats-overview,
                        .fi-modal-window {
                            position: relative;
                            z-index: 1;
                        }

                        .fi-simple-header {
                            align-items: flex-start;
                            text-align: left;
                        }

                        .fi-simple-header-heading,
                        .fi-section-header-heading,
                        .fi-wi-stats-overview-header-heading {
                            color: #f8fbff !important;
                        }

                        .fi-simple-header-subheading,
                        .fi-section-header-description,
                        .fi-wi-stats-overview-header-description,
                        .fi-sidebar-group-label,
                        .fi-sidebar-item-label,
                        .fi-topbar-item-label {
                            color: rgba(226, 232, 240, 0.72) !important;
                        }

                        .fi-sidebar-item-button,
                        .fi-sidebar-group-button,
                        .fi-topbar-item-button {
                            border-radius: 1rem !important;
                            transition: all 180ms ease;
                        }

                        .fi-sidebar-item-button:hover,
                        .fi-sidebar-group-button:hover,
                        .fi-topbar-item-button:hover,
                        .fi-topbar-item.fi-active .fi-topbar-item-button,
                        .fi-sidebar-item.fi-active .fi-sidebar-item-button {
                            background: linear-gradient(135deg, rgba(99, 102, 241, 0.24), rgba(45, 212, 191, 0.16)) !important;
                            color: #ffffff !important;
                            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.34);
                        }

                        .fi-input-wrp {
                            border-color: rgba(162, 0, 255, 0.42) !important;
                            background: rgba(15, 23, 42, 0.72) !important;
                            border-radius: 1rem !important;
                            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
                        }

                        .fi-input,
                        .fi-input-wrp-label {
                            color: #e2e8f0 !important;
                        }

                        .fi-btn {
                            border-radius: 1rem !important;
                        }

                        .fi-btn-color-primary {
                            background: linear-gradient(135deg, #a200ff, #d946ef) !important;
                            border: 1px solid rgba(234, 179, 255, 0.7) !important;
                            color: #ffffff !important;
                            box-shadow: 0 0 0 1px rgba(217, 70, 239, 0.38), 0 16px 34px rgba(162, 0, 255, 0.42);
                        }

                        .fi-btn-color-primary:hover {
                            filter: brightness(1.08);
                            box-shadow: 0 0 0 1px rgba(234, 179, 255, 0.85), 0 20px 36px rgba(162, 0, 255, 0.54);
                        }

                        .fi-wi-stats-overview-stat {
                            border-radius: 1.5rem !important;
                        }

                        .fi-wi-stats-overview-stat-value,
                        .fi-ta-text-item-label,
                        .fi-ta-header-cell-label,
                        .fi-breadcrumbs-item-label {
                            color: #f8fbff !important;
                        }

                        .fi-ta-ctn {
                            border-radius: 1.75rem !important;
                        }

                        .fi-ta-table thead {
                            background: rgba(99, 102, 241, 0.08);
                        }

                        .fi-ta-table tbody tr:hover {
                            background: rgba(148, 163, 184, 0.05);
                        }

                        .fi-dropdown-panel,
                        .fi-modal-window {
                            border-radius: 1.5rem !important;
                        }

                        .flux-topbar-badge {
                            display: flex;
                            align-items: center;
                            gap: 0.9rem;
                            padding: 0.7rem 0.9rem;
                            border-radius: 1.1rem;

                            color:rgba(162, 0, 255, 0.46);
                        }

                        .flux-topbar-badge-mark {
                            display: grid;
                            place-items: center;
                            width: 2.5rem;
                            height: 2.5rem;
                        }

                        .flux-topbar-badge-mark img {
                            width: 100%;
                            height: 100%;
                            object-fit: contain;
                        }

                        .flux-topbar-badge-copy {
                            display: grid;
                            gap: 0.1rem;
                        }

                        .flux-topbar-badge-copy strong {
                            color: #f8fbff;
                            font-size: 0.92rem;
                            line-height: 1.1;
                        }

                        .flux-topbar-badge-copy span {
                            color: rgba(226, 232, 240, 0.66);
                            font-size: 0.8rem;
                        }

                        .flux-user-chip {
                            display: grid;
                            gap: 0.08rem;
                            padding: 0.55rem 0.85rem;
                            border-radius: 0.9rem;
                            border: 1px solid rgba(162, 0, 255, 0.42);
                            background: rgba(15, 23, 42, 0.62);
                            text-align: right;
                            cursor: pointer;
                        }

                        .flux-user-chip strong {
                            color: #f8fbff;
                            font-size: 0.83rem;
                            line-height: 1.05;
                        }

                        .flux-user-chip span {
                            color: rgba(226, 232, 240, 0.66);
                            font-size: 0.73rem;
                            line-height: 1.1;
                        }

                        .flux-user-menu {
                            position: relative;
                        }

                        .flux-user-menu[open] .flux-user-chip {
                            border-color: rgba(234, 179, 255, 0.85);
                            box-shadow: 0 0 0 1px rgba(162, 0, 255, 0.3);
                        }

                        .flux-user-menu-dropdown {
                            position: absolute;
                            top: calc(100% + 0.55rem);
                            right: 0;
                            width: 12rem;
                            padding: 0.55rem;
                            border-radius: 0.8rem;
                            border: 1px solid rgba(162, 0, 255, 0.42);
                            background: rgba(8, 15, 28, 0.96);
                            box-shadow: 0 16px 32px rgba(2, 6, 23, 0.46);
                            z-index: 50;
                        }

                        .flux-user-menu-dropdown form {
                            margin: 0;
                        }

                        .flux-logout-button {
                            width: 100%;
                            border: 1px solid rgba(162, 0, 255, 0.5);
                            background: linear-gradient(135deg, #a200ff, #d946ef);
                            color: #ffffff;
                            border-radius: 0.65rem;
                            padding: 0.45rem 0.6rem;
                            font-size: 0.78rem;
                            font-weight: 600;
                            cursor: pointer;
                        }

                        .flux-sidebar-card {
                            display: grid;
                            gap: 1rem;
                            padding: 1rem;
                            border-radius: 1.35rem;
                            background: linear-gradient(180deg, rgba(30, 41, 59, 0.94), rgba(15, 23, 42, 0.74));
                            border: 1px solid rgba(162, 0, 255, 0.38);
                            color: #e2e8f0;
                        }

                        .flux-sidebar-card-header {
                            display: grid;
                            gap: 0.3rem;
                        }

                        .flux-sidebar-card-header strong {
                            font-size: 0.95rem;
                            color: #f8fbff;
                        }

                        .flux-sidebar-card-header span,
                        .flux-sidebar-card-metric span {
                            color: rgba(226, 232, 240, 0.64);
                            font-size: 0.78rem;
                        }

                        .flux-sidebar-card-grid {
                            display: grid;
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                            gap: 0.75rem;
                        }

                        .flux-sidebar-card-metric {
                            display: grid;
                            gap: 0.18rem;
                            padding: 0.8rem;
                            border-radius: 1rem;
                            background: rgba(255, 255, 255, 0.04);
                        }

                        .flux-sidebar-card-metric strong {
                            font-size: 1rem;
                            color: #f8fbff;
                        }

                        .flux-login-hero {
                            display: grid;
                            gap: 1.25rem;
                            padding: 1.4rem;
                            border-radius: 1.5rem;
                            background:
                                radial-gradient(circle at top right, rgba(255, 255, 255, 0.12), transparent 28%),
                                linear-gradient(135deg, rgba(30, 41, 59, 0.96), rgba(15, 23, 42, 0.9));
                            border: 1px solid rgba(162, 0, 255, 0.42);
                            color: #e2e8f0;
                        }

                        .flux-login-kicker {
                            display: inline-flex;
                            width: fit-content;
                            padding: 0.45rem 0.75rem;
                            border-radius: 999px;
                            background: rgba(99, 102, 241, 0.18);
                            color: #c7d2fe;
                            font-size: 0.74rem;
                            font-weight: 700;
                            letter-spacing: 0.08em;
                            text-transform: uppercase;
                        }

                        .flux-login-hero h2 {
                            margin: 0;
                            font-size: 1.65rem;
                            line-height: 1.1;
                            color: #f8fbff;
                        }

                        .flux-login-hero p {
                            margin: 0;
                            color: rgba(226, 232, 240, 0.72);
                            line-height: 1.6;
                        }

                        .flux-login-metrics {
                            display: grid;
                            grid-template-columns: repeat(3, minmax(0, 1fr));
                            gap: 0.75rem;
                        }

                        .flux-login-metric {
                            display: grid;
                            gap: 0.2rem;
                            padding: 0.9rem;
                            border-radius: 1rem;
                            background: rgba(255, 255, 255, 0.04);
                        }

                        .flux-login-metric strong {
                            color: #f8fbff;
                            font-size: 1rem;
                        }

                        .flux-login-metric span {
                            color: rgba(226, 232, 240, 0.64);
                            font-size: 0.78rem;
                            line-height: 1.4;
                        }

                        @media (max-width: 1023px) {
                            .fi-sidebar {
                                padding-left: 0;
                            }

                            .fi-sidebar-header,
                            .fi-sidebar-nav,
                            .fi-topbar nav {
                                margin-inline: 0.75rem;
                            }

                            .fi-main {
                                padding-inline: 0.75rem !important;
                            }

                            .flux-topbar-badge {
                                display: none;
                            }

                            .flux-login-metrics {
                                grid-template-columns: 1fr;
                            }
                        }
                    </style>
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                   
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                   
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <div class="flux-topbar-badge">
                        <div class="flux-topbar-badge-mark">
                            <img src="/images/logo-locacao.svg" alt="Logo Sistema de Locação">
                        </div>
                        <div class="flux-topbar-badge-copy">
                            <strong>Sistema de Locação</strong>
                        </div>
                    </div>
                HTML),
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn (): HtmlString => new HtmlString(
                    '<details class="flux-user-menu">'.
                        '<summary class="flux-user-chip"><strong>'.e(auth()->user()?->name ?? 'Usuário').'</strong><span>'.e(auth()->user()?->email ?? '').'</span></summary>'.
                        '<div class="flux-user-menu-dropdown">'.
                            '<form method="POST" action="'.e(filament()->getLogoutUrl()).'">'.
                                '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'.
                                '<button type="submit" class="flux-logout-button">Sair</button>'.
                            '</form>'.
                        '</div>'.
                    '</details>'
                ),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
