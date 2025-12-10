<?php

namespace App\Filament\Resources\VendorRentalResource\Pages;

use App\Filament\Resources\VendorRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorRental extends EditRecord
{
    protected static string $resource = VendorRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
