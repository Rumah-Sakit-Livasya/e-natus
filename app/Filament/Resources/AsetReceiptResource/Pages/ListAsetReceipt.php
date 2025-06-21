<?php

namespace App\Filament\Resources\AsetReceiptResource\Pages;

use App\Filament\Resources\AsetReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsetReceipt extends ListRecords
{
    protected static string $resource = AsetReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
