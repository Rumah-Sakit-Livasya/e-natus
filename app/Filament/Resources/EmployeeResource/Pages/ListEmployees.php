<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Actions\Action; // <-- TAMBAHKAN USE STATEMENT INI
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            // Ini adalah tombol baru yang kita buat
            Action::make('report')
                ->label('Laporan Absensi')
                ->icon('heroicon-o-chart-bar-square')
                ->color('info') // Anda bisa ganti warnanya
                ->url(EmployeeResource::getUrl('report')),
        ];
    }
}
