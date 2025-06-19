<?php

namespace App\Filament\Resources\SdmResource\Pages;

use App\Filament\Resources\SdmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSdm extends EditRecord
{
    protected static string $resource = SdmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }
}
