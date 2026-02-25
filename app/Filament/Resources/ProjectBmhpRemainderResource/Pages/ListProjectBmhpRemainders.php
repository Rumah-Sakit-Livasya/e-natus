<?php

namespace App\Filament\Resources\ProjectBmhpRemainderResource\Pages;

use App\Filament\Resources\ProjectBmhpRemainderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectBmhpRemainders extends ListRecords
{
    protected static string $resource = ProjectBmhpRemainderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
