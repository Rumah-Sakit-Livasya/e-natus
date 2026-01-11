<?php

namespace App\Filament\Resources\AsetReceiptResource\Pages;

use App\Filament\Resources\AsetReceiptResource;
use App\Models\Aset;
use Filament\Resources\Pages\CreateRecord;

class CreateAsetReceipt extends CreateRecord
{
    protected static string $resource = AsetReceiptResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }

    protected function afterCreate(): void
    {
        \Log::info('afterCreate dipanggil untuk AssetReceipt ID: ' . $this->record->id);

        try {
            $this->record->load('receiptItems');

            foreach ($this->record->receiptItems as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    Aset::create([
                        'template_id'   => $item->template_id,
                        'lander_id'     => $item->lander_id,
                        'custom_name'   => $item->custom_name,
                        'code'          => null, // akan digenerate otomatis di model Aset
                        'condition'     => 'baik',
                        'brand'         => $item->brand,
                        'purchase_year' => $item->purchase_year,
                        'tarif'         => $item->tarif,
                        'satuan'        => $item->satuan,
                        'status'        => 'available',
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Gagal buat aset otomatis: ' . $e->getMessage());
            throw $e; // hilangkan jika ingin fail silent
        }
    }
}
