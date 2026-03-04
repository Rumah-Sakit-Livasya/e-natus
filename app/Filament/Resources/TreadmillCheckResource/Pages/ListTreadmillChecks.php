<?php

namespace App\Filament\Resources\TreadmillCheckResource\Pages;

use App\Filament\Resources\TreadmillCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreadmillChecks extends ListRecords
{
    protected static string $resource = TreadmillCheckResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ParticipantResource::getUrl() => 'Participants',
        ];
    }
}
