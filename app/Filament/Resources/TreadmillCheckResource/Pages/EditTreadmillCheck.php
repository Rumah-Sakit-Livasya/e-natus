<?php

namespace App\Filament\Resources\TreadmillCheckResource\Pages;

use App\Filament\Resources\TreadmillCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreadmillCheck extends EditRecord
{
    protected static string $resource = TreadmillCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
