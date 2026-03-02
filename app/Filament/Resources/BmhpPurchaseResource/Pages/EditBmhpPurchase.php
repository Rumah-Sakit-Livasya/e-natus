<?php

namespace App\Filament\Resources\BmhpPurchaseResource\Pages;

use App\Filament\Resources\BmhpPurchaseResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBmhpPurchase extends EditRecord
{
    protected static string $resource = BmhpPurchaseResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        abort_unless($this->record->status === 'pending', 403);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pembelian berhasil diperbarui')
            ->body('Data pembelian BHP telah disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
