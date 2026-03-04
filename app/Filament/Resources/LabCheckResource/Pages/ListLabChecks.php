<?php

namespace App\Filament\Resources\LabCheckResource\Pages;

use App\Filament\Resources\LabCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLabChecks extends ListRecords
{
    protected static string $resource = LabCheckResource::class;

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
