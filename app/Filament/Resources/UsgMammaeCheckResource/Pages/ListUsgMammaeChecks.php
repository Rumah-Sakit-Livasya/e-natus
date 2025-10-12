<?php

namespace App\Filament\Resources\UsgMammaeCheckResource\Pages;

use App\Filament\Resources\UsgMammaeCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsgMammaeChecks extends ListRecords
{
    protected static string $resource = UsgMammaeCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
