<?php

namespace App\Filament\Resources\LanderResource\Pages;

use App\Filament\Resources\LanderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLander extends CreateRecord
{
    protected static string $resource = LanderResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }
}
