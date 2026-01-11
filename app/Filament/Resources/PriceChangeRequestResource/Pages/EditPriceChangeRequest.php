<?php

namespace App\Filament\Resources\PriceChangeRequestResource\Pages;

use App\Filament\Resources\PriceChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceChangeRequest extends EditRecord
{
    protected static string $resource = PriceChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
