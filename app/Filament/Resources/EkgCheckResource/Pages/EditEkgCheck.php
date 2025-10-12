<?php

namespace App\Filament\Resources\EkgCheckResource\Pages;

use App\Filament\Resources\EkgCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEkgCheck extends EditRecord
{
    protected static string $resource = EkgCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
