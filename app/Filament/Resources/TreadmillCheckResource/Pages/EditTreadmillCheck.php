<?php

namespace App\Filament\Resources\TreadmillCheckResource\Pages;

use App\Filament\Resources\TreadmillCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreadmillCheck extends EditRecord
{
    protected static string $resource = TreadmillCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ParticipantResource::getUrl() => 'Participants',
        ];
    }
}
