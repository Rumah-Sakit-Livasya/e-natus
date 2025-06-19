<?php

namespace App\Filament\Resources\SdmResource\Pages;

use App\Filament\Resources\SdmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSdms extends ListRecords
{
    protected static string $resource = SdmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
