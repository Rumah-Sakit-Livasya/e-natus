<?php

namespace App\Notifications;

use App\Filament\Resources\BmhpPurchaseResource;
use App\Models\BmhpPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

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
        $supplierName = $this->purchase->supplier?->name;
        $tanggal = optional($this->purchase->tanggal_pembelian)->format('d/m/Y');
        $supplierLabel = $supplierName ? " (Supplier: {$supplierName})" : '';

        return new DatabaseMessage([
            'format' => 'filament',
            'title' => 'Persetujuan Pembelian BHP',
            'message' => "Pengajuan pembelian BHP tanggal {$tanggal}{$supplierLabel} membutuhkan persetujuan.",
            'url' => BmhpPurchaseResource::getUrl('edit', ['record' => $this->purchase]),
            'is_approvable' => true,
            'record_model' => BmhpPurchase::class,
            'record_id' => $this->purchase->id,
        ]);
    }
}
