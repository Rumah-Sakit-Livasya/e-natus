<?php

namespace App\Filament\Resources\SupplierReceiptResource\Pages;

use App\Filament\Resources\SupplierReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierReceipts extends ListRecords
{
    protected static string $resource = SupplierReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
