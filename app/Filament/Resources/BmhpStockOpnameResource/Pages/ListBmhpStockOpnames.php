<?php

namespace App\Filament\Resources\BmhpStockOpnameResource\Pages;

use App\Filament\Resources\BmhpStockOpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBmhpStockOpnames extends ListRecords
{
    protected static string $resource = BmhpStockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
