<?php

namespace App\Filament\Resources\BmhpStockOpnameResource\Pages;

use App\Filament\Resources\BmhpStockOpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBmhpStockOpname extends EditRecord
{
    protected static string $resource = BmhpStockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
