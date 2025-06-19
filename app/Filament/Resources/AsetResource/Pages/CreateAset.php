<?php

namespace App\Filament\Resources\AsetResource\Pages;

use App\Filament\Resources\AsetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAset extends CreateRecord
{
    protected static string $resource = AsetResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Redirect ke halaman list setelah create
    }
}
