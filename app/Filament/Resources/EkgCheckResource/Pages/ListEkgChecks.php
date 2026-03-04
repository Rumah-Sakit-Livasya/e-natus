<?php

namespace App\Filament\Resources\EkgCheckResource\Pages;

use App\Filament\Resources\EkgCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEkgChecks extends ListRecords
{
    protected static string $resource = EkgCheckResource::class;

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
