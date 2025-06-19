<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ClientRegionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Klien per Region';

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $data = Client::select('regions.name', DB::raw('count(*) as total'))
            ->join('regions', 'clients.region_id', '=', 'regions.id')
            ->groupBy('regions.name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Klien',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
