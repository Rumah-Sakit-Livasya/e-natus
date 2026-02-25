<?php

namespace App\Filament\Resources\BmhpResource\Pages;

use App\Filament\Resources\BmhpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBmhp extends EditRecord
{
    protected static string $resource = BmhpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
