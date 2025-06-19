<?php

namespace App\Filament\Resources\AsetReceiptResource\Pages;

use App\Filament\Resources\AsetReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsetReceipt extends EditRecord
{
    protected static string $resource = AsetReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
