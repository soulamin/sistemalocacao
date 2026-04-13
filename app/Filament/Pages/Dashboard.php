<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OperationalKpiChart;
use App\Filament\Widgets\RentalOverviewStats;
use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Painel de controle';

    public function getColumns(): int|string|array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    public function getHeader(): ?View
    {
        $productCount = Product::count();
        $rentedProductCount = Product::where('status', 'locado')->count();
        $activeRentalCount = Rental::where('status', 'ativa')->count();
        $occupancyRate = $productCount > 0 ? round(($rentedProductCount / $productCount) * 100) : 0;

        return view('filament.pages.dashboard-header', [
            'productCount' => $productCount,
            'clientCount' => Client::count(),
            'activeRentalCount' => $activeRentalCount,
            'occupancyRate' => $occupancyRate,
            'activeRevenue' => Rental::where('status', 'ativa')->sum('valor_total'),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            RentalOverviewStats::class,
            OperationalKpiChart::class,
        ];
    }
}
