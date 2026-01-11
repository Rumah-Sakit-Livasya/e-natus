<?php

namespace App\Filament\Resources\VendorRentalResource\Pages;

use App\Filament\Resources\VendorRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorRentals extends ListRecords
{
    protected static string $resource = VendorRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
