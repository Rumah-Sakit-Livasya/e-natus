<?php

namespace App\Filament\Resources\BmhpResource\Pages;

use App\Filament\Resources\BmhpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBmhps extends ListRecords
{
    protected static string $resource = BmhpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
