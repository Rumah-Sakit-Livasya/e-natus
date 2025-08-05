<?php

namespace App\Filament\Resources\McuResultResource\Pages;

use App\Filament\Resources\McuResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMcuResult extends EditRecord
{
    protected static string $resource = McuResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
