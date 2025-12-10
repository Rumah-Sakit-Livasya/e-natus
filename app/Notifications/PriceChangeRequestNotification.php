<?php

namespace App\Notifications;

use App\Models\PriceChangeRequest;
use App\Filament\Resources\PriceChangeRequestResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class PriceChangeRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PriceChangeRequest $priceChangeRequest
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $userName = $this->priceChangeRequest->requester?->name ?? 'User';
        $currentPrice = number_format($this->priceChangeRequest->current_price, 0, ',', '.');
        $requestedPrice = number_format($this->priceChangeRequest->requested_price, 0, ',', '.');

        return new DatabaseMessage([
            'format' => 'filament',
            'title' => 'Permintaan Perubahan Harga',
            'message' => "{$userName} meminta perubahan harga dari Rp {$currentPrice} ke Rp {$requestedPrice}",
            'url' => PriceChangeRequestResource::getUrl('index'),
            'is_approvable' => true,
            'record_model' => PriceChangeRequest::class,
            'record_id' => $this->priceChangeRequest->id,
        ]);
    }
}
