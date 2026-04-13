<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\RentalResource;
use App\Models\Client;
use App\Models\Product;
use App\Models\Rental;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentalOverviewStats extends StatsOverviewWidget
{
    protected ?string $heading = 'KPIs operacionais';

    protected ?string $description = 'Acompanhamento rápido do estoque, da base de clientes e das locações ativas.';

    protected function getStats(): array
    {
        $productCount = Product::count();
        $availableProductCount = Product::where('status', 'disponivel')->count();
        $rentedProductCount = Product::where('status', 'locado')->count();
        $activeRentalCount = Rental::where('status', 'ativa')->count();

        return [
            Stat::make('Equipamentos cadastrados', number_format($productCount, 0, ',', '.'))
                ->description($availableProductCount.' disponíveis para novas locações')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart($this->getMonthlyTotals(Product::class))
                ->url(ProductResource::getUrl('index')),
            Stat::make('Clientes cadastrados', number_format(Client::count(), 0, ',', '.'))
                ->description($activeRentalCount.' contratos ativos em andamento')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($this->getMonthlyTotals(Client::class))
                ->url(ClientResource::getUrl('index')),
            Stat::make('Equipamentos locados', number_format($rentedProductCount, 0, ',', '.'))
                ->description($activeRentalCount.' locações ativas no painel')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart($this->getMonthlyRentalItems())
                ->url(RentalResource::getUrl('index')),
        ];
    }

    protected function getMonthlyTotals(string $modelClass): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        return collect(range(0, 5))
            ->map(fn (int $offset): int => $modelClass::whereBetween('created_at', [
                $start->copy()->addMonths($offset)->startOfMonth(),
                $start->copy()->addMonths($offset)->endOfMonth(),
            ])->count())
            ->all();
    }

    protected function getMonthlyRentalItems(): array
    {
        $start = now()->startOfMonth()->subMonths(5);

        return collect(range(0, 5))
            ->map(function (int $offset) use ($start): int {
                $monthStart = $start->copy()->addMonths($offset)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();

                return Rental::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->withCount('products')
                    ->get()
                    ->sum('products_count');
            })
            ->all();
    }
}
