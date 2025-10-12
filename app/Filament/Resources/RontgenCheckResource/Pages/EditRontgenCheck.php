<?php

namespace App\Filament\Resources\RontgenCheckResource\Pages;

use App\Filament\Resources\RontgenCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRontgenCheck extends EditRecord
{
    protected static string $resource = RontgenCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
