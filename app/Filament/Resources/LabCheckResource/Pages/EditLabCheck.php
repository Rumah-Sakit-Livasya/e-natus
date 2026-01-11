<?php

namespace App\Filament\Resources\LabCheckResource\Pages;

use App\Filament\Resources\LabCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLabCheck extends EditRecord
{
    protected static string $resource = LabCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
