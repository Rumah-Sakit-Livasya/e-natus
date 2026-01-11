<?php

namespace App\Filament\Resources\McuResultResource\Pages;

use App\Filament\Resources\McuResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMcuResults extends ListRecords
{
    protected static string $resource = McuResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
