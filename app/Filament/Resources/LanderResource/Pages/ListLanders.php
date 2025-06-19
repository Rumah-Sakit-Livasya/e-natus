<?php

namespace App\Filament\Resources\LanderResource\Pages;

use App\Filament\Resources\LanderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanders extends ListRecords
{
    protected static string $resource = LanderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
