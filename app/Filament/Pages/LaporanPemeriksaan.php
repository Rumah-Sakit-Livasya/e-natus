<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\HasilPemeriksaanWidget;

class LaporanPemeriksaan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string $view = 'filament.pages.laporan-pemeriksaan';

    // Menempatkan halaman ini di grup menu baru
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan per Pemeriksaan';
    protected static ?int $navigationSort = 1;

    // Hanya user yang memiliki permission 'view hasil mcu' yang dapat melihat halaman ini
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->can('view hasil mcu');
    }

    // Metode ini akan menampilkan widget tabel kita
    protected function getHeaderWidgets(): array
    {
        return [
            HasilPemeriksaanWidget::class,
        ];
    }
}
