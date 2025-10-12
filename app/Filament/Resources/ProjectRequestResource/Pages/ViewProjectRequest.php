<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\Concerns\HasRelationManagers;

class ViewProjectRequest extends ViewRecord
{
    use HasRelationManagers;

    protected static string $resource = ProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // Paksa tab relasi (Participants) bisa tambah & edit peserta dari View
    protected function getRelationManagerNames(): array
    {
        // Return all relation managers, ensures ParticipantsRelationManager aktif
        return static::getResource()::getRelations();
    }
}
