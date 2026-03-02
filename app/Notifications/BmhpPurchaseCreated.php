<?php

namespace App\Notifications;

use App\Filament\Resources\BmhpPurchaseResource;
use App\Models\BmhpPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class BmhpPurchaseCreated extends Notification
{
    use Queueable;

    public function __construct(public BmhpPurchase $purchase) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $this->purchase->loadMissing('items.bmhp');

        $supplierName = $this->purchase->supplier?->name;
        $tanggal = optional($this->purchase->tanggal_pembelian)->format('d/m/Y');
        $supplierLabel = $supplierName ? " (Supplier: {$supplierName})" : '';
        $items = $this->purchase->items
            ->map(function ($item) {
                $name = $item->bmhp?->name ?? 'BMHP tidak ditemukan';
                $qty = (int) ($item->qty ?? 0);
                $purchaseType = (string) ($item->purchase_type ?? 'pcs');
                $unitLabel = $purchaseType === 'unit' ? 'unit' : 'pcs';

                return [
                    'label' => "{$name} - {$qty} {$unitLabel}",
                    'is_checked' => (bool) ($item->is_checked ?? true),
                ];
            })
            ->values()
            ->all();
        $notaPath = $this->purchase->nota_pembelian;
        $notaUrl = $notaPath ? Storage::disk('public')->url($notaPath) : null;

        return new DatabaseMessage([
            'format' => 'filament',
            'title' => 'Persetujuan Pembelian BHP',
            'message' => "Pengajuan pembelian BHP tanggal {$tanggal}{$supplierLabel} membutuhkan persetujuan.",
            'purchased_items' => $items,
            'nota_pembelian' => $notaPath,
            'nota_pembelian_url' => $notaUrl,
            'url' => BmhpPurchaseResource::getUrl('edit', ['record' => $this->purchase]),
            'is_approvable' => true,
            'record_model' => BmhpPurchase::class,
            'record_id' => $this->purchase->id,
        ]);
    }
}
