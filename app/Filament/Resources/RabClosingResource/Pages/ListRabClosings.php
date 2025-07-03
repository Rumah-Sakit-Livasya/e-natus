<?php

namespace App\Filament\Resources\RabClosingResource\Pages;

use App\Filament\Resources\RabClosingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRabClosings extends ListRecords
{
    protected static string $resource = RabClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
