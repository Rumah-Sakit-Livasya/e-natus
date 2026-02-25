<?php

namespace App\Filament\Resources\BmhpPurchaseResource\Pages;

use App\Filament\Resources\BmhpPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBmhpPurchases extends ListRecords
{
    protected static string $resource = BmhpPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
