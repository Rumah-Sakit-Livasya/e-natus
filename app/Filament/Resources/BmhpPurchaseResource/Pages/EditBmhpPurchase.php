<?php

namespace App\Filament\Resources\BmhpPurchaseResource\Pages;

use App\Filament\Resources\BmhpPurchaseResource;
use App\Models\Bmhp;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditBmhpPurchase extends EditRecord
{
    protected static string $resource = BmhpPurchaseResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        abort_unless($this->record->status === 'pending', 403);
    }

    protected function afterSave(): void
    {
        $state = $this->form->getState();

        $this->record->items()->delete();

        $items = $state['items'] ?? [];
        if (!is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            $bmhpId = $item['bmhp_id'] ?? null;
            $purchaseType = (string) ($item['purchase_type'] ?? 'pcs');
            $qty = (int) ($item['qty'] ?? 0);
            $harga = (float) ($item['harga'] ?? 0);

            $bmhp = $bmhpId ? Bmhp::find($bmhpId) : null;
            if (!$bmhp) {
                throw ValidationException::withMessages([
                    "items.{$index}.bmhp_id" => 'BMHP wajib dipilih.',
                ]);
            }

            $pcsPerUnit = (int) ($bmhp->pcs_per_unit ?? 0);
            if ($purchaseType === 'unit' && $pcsPerUnit <= 0) {
                throw ValidationException::withMessages([
                    "items.{$index}.bmhp_id" => 'BMHP ini belum memiliki Isi per Kemasan (pcs), tidak bisa beli per unit.',
                ]);
            }

            $totalPcs = $purchaseType === 'pcs'
                ? $qty
                : ($qty * $pcsPerUnit);

            $subtotal = $harga;

            $this->record->items()->create([
                'bmhp_id' => $bmhp->id,
                'purchase_type' => $purchaseType,
                'qty' => $qty,
                'pcs_per_unit_snapshot' => $purchaseType === 'unit' ? $pcsPerUnit : null,
                'total_pcs' => $totalPcs,
                'harga' => $harga,
                'subtotal' => $subtotal,
            ]);
        }
    }
}
