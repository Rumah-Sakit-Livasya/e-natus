<?php

namespace App\Filament\Resources\DrugTestResource\Pages;

use App\Filament\Resources\DrugTestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrugTests extends ListRecords
{
    protected static string $resource = DrugTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
