<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\HasilPemeriksaanWidget;

class LaporanPemeriksaan extends Page
{
    protected static ?string $cluster = \App\Filament\Clusters\MedicalCheckUpCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string $view = 'filament.pages.laporan-pemeriksaan';

    // Menempatkan halaman ini di grup menu baru
    protected static ?string $navigationGroup = 'Medical Check Up';
    protected static ?string $navigationLabel = 'Laporan per Pemeriksaan';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view laporan pemeriksaan');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    // Metode ini akan menampilkan widget tabel kita
    protected function getHeaderWidgets(): array
    {
        return [
            HasilPemeriksaanWidget::class,
        ];
    }
}
