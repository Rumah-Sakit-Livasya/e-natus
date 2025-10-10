<?php

namespace App\Notifications;

use App\Filament\Resources\BmhpStockOpnameResource;
use App\Models\BmhpStockOpname;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class StockOpnameCreated extends Notification
{
    use Queueable;

    public function __construct(public BmhpStockOpname $BmhpStockOpname) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $bmhpName = $this->BmhpStockOpname->bmhp?->name ?? 'N/A';
        $userName = $this->BmhpStockOpname->user?->name ?? 'Sistem';

        return new DatabaseMessage([
            'title' => 'Persetujuan Stock Opname',
            'message' => "{$userName} mengajukan stock opname untuk {$bmhpName}. Mohon ditinjau.",
            'url' => BmhpStockOpnameResource::getUrl('edit', ['record' => $this->BmhpStockOpname]),
            'is_approvable' => true,
            'record_model' => BmhpStockOpname::class,
            'record_id' => $this->BmhpStockOpname->id,
        ]);
    }
}
