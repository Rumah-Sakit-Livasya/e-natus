<?php

namespace App\Filament\Resources\LanderResource\Pages;

use App\Filament\Resources\LanderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLander extends EditRecord
{
    protected static string $resource = LanderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Kembali ke halaman list
    }
}
