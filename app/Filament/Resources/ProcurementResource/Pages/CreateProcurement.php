<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use Filament\Resources\Pages\CreateRecord;

class CreateProcurement extends CreateRecord
{
    protected static string $resource = \App\Filament\Resources\ProcurementResource::class;

    protected function afterCreate(): void
    {
        $state = $this->form->getState();
        \Log::info('Procurement items data:', $state['items'] ?? []);

        if (!empty($state['items']) && is_array($state['items'])) {
            foreach ($state['items'] as $item) {
                $this->record->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'unit' => $item['unit'],
                    'harga_pengajuan' => $item['harga_pengajuan'],
                    'qty_pengajuan' => $item['qty_pengajuan'],
                    'satuan' => $item['satuan'],
                    'status' => $item['status'] ?? null,
                    // jangan isi jumlah_pengajuan di sini
                ]);
            }
        }
    }
}
