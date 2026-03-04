<?php

namespace App\Filament\Resources\SupplierReceiptResource\Pages;

use App\Filament\Resources\SupplierReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierReceipt extends EditRecord
{
    protected static string $resource = SupplierReceiptResource::class;

    public function mount(int | string $record): void
    {
        abort_unless(SupplierReceiptResource::canViewAny(), 403);

        parent::mount($record);
    }

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;

        if (!$record) {
            return false;
        }

        return SupplierReceiptResource::canEdit($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
