<?php

namespace App\Filament\Resources\RontgenCheckResource\Pages;

use App\Filament\Resources\RontgenCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRontgenChecks extends ListRecords
{
    protected static string $resource = RontgenCheckResource::class;

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
