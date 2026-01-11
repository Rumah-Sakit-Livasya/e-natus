<?php

namespace App\Filament\Resources\SupplierReceiptResource\Pages;

use App\Filament\Resources\SupplierReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierReceipt extends EditRecord
{
    protected static string $resource = SupplierReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
