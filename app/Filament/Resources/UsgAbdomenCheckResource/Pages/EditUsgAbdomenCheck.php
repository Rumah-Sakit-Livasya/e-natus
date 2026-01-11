<?php

namespace App\Filament\Resources\UsgAbdomenCheckResource\Pages;

use App\Filament\Resources\UsgAbdomenCheckResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsgAbdomenCheck extends EditRecord
{
    protected static string $resource = UsgAbdomenCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
