<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\HasilPemeriksaanWidget; // <-- Kita akan buat ini nanti

class LaporanPemeriksaan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string $view = 'filament.pages.laporan-pemeriksaan';

    // Menempatkan halaman ini di grup menu baru
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan per Pemeriksaan';
    protected static ?int $navigationSort = 1;

    // Metode ini akan menampilkan widget tabel kita
    protected function getHeaderWidgets(): array
    {
        return [
            HasilPemeriksaanWidget::class,
        ];
    }
}
