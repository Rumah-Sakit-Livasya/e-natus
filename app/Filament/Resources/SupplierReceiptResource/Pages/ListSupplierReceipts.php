<?php

namespace App\Filament\Resources\SupplierReceiptResource\Pages;

use App\Filament\Resources\SupplierReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierReceipts extends ListRecords
{
    protected static string $resource = SupplierReceiptResource::class;

    public function mount(): void
    {
        abort_unless(SupplierReceiptResource::canViewAny(), 403);

        parent::mount();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return SupplierReceiptResource::canViewAny();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
