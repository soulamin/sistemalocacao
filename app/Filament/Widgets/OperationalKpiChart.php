<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Product;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class OperationalKpiChart extends ChartWidget
{
    protected static ?string $heading = 'Visão operacional';

    protected static ?string $description = 'Comparativo visual entre estoque, base de clientes e equipamentos locados.';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $productCount = Product::count();
        $clientCount = Client::count();
        $rentedProductCount = Product::where('status', 'locado')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Indicadores',
                    'data' => [$productCount, $clientCount, $rentedProductCount],
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.78)',
                        'rgba(16, 185, 129, 0.78)',
                        'rgba(245, 158, 11, 0.78)',
                    ],
                    'borderColor' => [
                        'rgb(129, 140, 248)',
                        'rgb(52, 211, 153)',
                        'rgb(251, 191, 36)',
                    ],
                    'borderRadius' => 18,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                'Equipamentos cadastrados',
                'Clientes',
                'Equipamentos locados',
            ],
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => '#cbd5e1',
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.14)',
                    ],
                    'ticks' => [
                        'color' => '#94a3b8',
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
