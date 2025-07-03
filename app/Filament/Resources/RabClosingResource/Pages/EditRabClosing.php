<?php

namespace App\Filament\Resources\RabClosingResource\Pages;

use App\Filament\Resources\RabClosingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRabClosing extends EditRecord
{
    protected static string $resource = RabClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     // Ambil ulang record untuk memastikan data paling update
    //     $record = $this->getRecord();

    //     // Isi data secara manual ke array form
    //     $data['total_anggaran'] = 'Rp ' . number_format($record->total_anggaran, 0, ',', '.');
    //     $data['total_realisasi'] = 'Rp ' . number_format($record->total_realisasi, 0, ',', '.');
    //     $data['selisih'] = 'Rp ' . number_format($record->selisih, 0, ',', '.');

    //     return $data;
    // }
}
