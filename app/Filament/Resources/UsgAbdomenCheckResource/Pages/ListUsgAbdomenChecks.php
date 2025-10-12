<?php

namespace App\Filament\Resources\UsgAbdomenCheckResource\Pages;

use App\Filament\Resources\UsgAbdomenCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsgAbdomenChecks extends ListRecords
{
    protected static string $resource = UsgAbdomenCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
