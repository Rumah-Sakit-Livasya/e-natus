<?php

namespace App\Filament\Resources\SdmResource\Pages;

use App\Filament\Resources\SdmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSdm extends CreateRecord
{
    protected static string $resource = SdmResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }
}
