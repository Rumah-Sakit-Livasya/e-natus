<?php

namespace App\Filament\Resources\BmhpOfficeUsageResource\Pages;

use App\Filament\Resources\BmhpOfficeUsageResource;
use Filament\Resources\Pages\ListRecords;

class ListBmhpOfficeUsages extends ListRecords
{
    protected static string $resource = BmhpOfficeUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
