<?php

namespace App\Filament\Resources\SpirometryCheckResource\Pages;

use App\Filament\Resources\SpirometryCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpirometryChecks extends ListRecords
{
    protected static string $resource = SpirometryCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
