<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use Filament\Resources\Pages\EditRecord;

class EditProcurement extends EditRecord
{
    protected static string $resource = \App\Filament\Resources\ProcurementResource::class;

    protected function afterSave(): void
    {
        $state = $this->form->getState();

        $this->record->items()->delete();

        if (!empty($state['items']) && is_array($state['items'])) {
            foreach ($state['items'] as $item) {
                $this->record->items()->create([
                    'nama_barang' => $item['nama_barang'],
                    'unit' => $item['unit'],
                    'harga_pengajuan' => $item['harga_pengajuan'],
                    'qty_pengajuan' => $item['qty_pengajuan'],
                    'satuan' => $item['satuan'],
                    'status' => $item['status'] ?? null,
                ]);
            }
        }
    }
}
