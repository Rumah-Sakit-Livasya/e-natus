<?php

namespace App\Filament\Resources\SupplierReceiptResource\Pages;

use App\Filament\Resources\SupplierReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplierReceipt extends CreateRecord
{
    protected static string $resource = SupplierReceiptResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }
}
