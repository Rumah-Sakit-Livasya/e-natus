<?php

namespace App\Filament\Resources\SpirometryCheckResource\Pages;

use App\Filament\Resources\SpirometryCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpirometryCheck extends EditRecord
{
    protected static string $resource = SpirometryCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
