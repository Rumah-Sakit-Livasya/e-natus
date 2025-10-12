<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAudiometryChecks extends ListRecords
{
    protected static string $resource = AudiometryCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
