<?php

namespace App\Filament\Resources\BmhpStockStatusResource\Pages;

use App\Filament\Resources\BmhpStockStatusResource;
use Filament\Resources\Pages\ListRecords;

class ListBmhpStockStatuses extends ListRecords
{
    protected static string $resource = BmhpStockStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
