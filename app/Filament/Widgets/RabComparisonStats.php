<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model; // Impor ini

class RabComparisonStats extends BaseWidget
{
    /**
     * Jadikan properti ini bisa menerima record model dari luar.
     */
    public ?Model $record = null;

    /**
     * Nonaktifkan lazy loading agar data selalu tersedia saat dibutuhkan.
     */
    protected static bool $isLazy = false;

    /**
     * Metode getStats() akan membuat kartu-kartu statistik.
     */
    protected function getStats(): array
    {
        // Jika tidak ada record proyek, jangan tampilkan apa-apa
        if (!$this->record || !$this->record->rabClosing) {
            return [];
        }

        $rabClosing = $this->record->rabClosing;

        return [
            Stat::make('Total Anggaran (Closing)', 'Rp ' . number_format($rabClosing->total_anggaran, 0, ',', '.'))
                ->description('Anggaran final yang disetujui'),

            Stat::make('Total Realisasi', 'Rp ' . number_format($rabClosing->total_realisasi, 0, ',', '.'))
                ->description('Total pengeluaran aktual')
                ->color($rabClosing->selisih >= 0 ? 'success' : 'danger'),

            Stat::make('Selisih (Hemat/Boros)', 'Rp ' . number_format(abs($rabClosing->selisih), 0, ',', '.'))
                ->description($rabClosing->selisih >= 0 ? 'Pengeluaran lebih hemat' : 'Pengeluaran melebihi anggaran')
                ->descriptionIcon($rabClosing->selisih >= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($rabClosing->selisih >= 0 ? 'success' : 'danger'),
        ];
    }
}
