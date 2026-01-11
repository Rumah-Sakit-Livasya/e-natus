<?php

namespace App\Filament\Widgets;

use App\Models\Aset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Aset', Aset::count())
                ->description('Jumlah seluruh aset')
                ->descriptionIcon('heroicon-m-cube'),

            Stat::make('Aset Tersedia', Aset::where('status', 'available')->count())
                ->description('Aset siap digunakan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Aset Terpakai', Aset::where('status', 'unavailable')->count())
                ->description('Aset sedang digunakan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
