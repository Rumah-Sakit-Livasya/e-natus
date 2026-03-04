<?php

namespace App\Filament\Resources\DokterResource\Pages;

use App\Filament\Resources\DokterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDokter extends CreateRecord
{
    protected static string $resource = DokterResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl();
    }
}
