<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportStatsOverview extends BaseWidget
{
    /**
     * Properti ini akan menerima data dari halaman laporan.
     * Atribut #[Locked] memastikan properti ini tidak bisa diubah dari frontend.
     */
    #[Locked]
    public ?array $stats = [];

    /**
     * Kita atur agar widget ini tidak muncul di dashboard secara otomatis.
     * Ia hanya akan muncul saat kita panggil secara manual.
     */
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        // Jika tidak ada data yang dikirim, kembalikan array kosong.
        if (empty($this->stats)) {
            return [];
        }

        // Buat kartu statistik dari data yang sudah dihitung.
        return [
            Stat::make('Total Penugasan', $this->stats['total_working_days'])
                ->icon('heroicon-o-briefcase'),

            Stat::make('Hadir', $this->stats['present_days'])
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Tidak Hadir', $this->stats['absent_days'])
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Tingkat Kehadiran', $this->stats['attendance_rate'] . '%')
                ->icon('heroicon-o-chart-pie')
                ->color('info'),
        ];
    }
}
