<?php

namespace App\Filament\Widgets;

use App\Models\ProjectRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Project', ProjectRequest::count())
                ->description('Total keseluruhan project')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Project Aktif', ProjectRequest::where('status', 'active')->count())
                ->description('Project yang sedang berjalan')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),

            Stat::make('Menunggu Persetujuan', ProjectRequest::where('status', 'pending')->count())
                ->description('Project yang butuh persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
