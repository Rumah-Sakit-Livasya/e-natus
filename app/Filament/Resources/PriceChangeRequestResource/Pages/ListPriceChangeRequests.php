<?php

namespace App\Filament\Resources\PriceChangeRequestResource\Pages;

use App\Filament\Resources\PriceChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPriceChangeRequests extends ListRecords
{
    protected static string $resource = PriceChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
