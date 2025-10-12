<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectRequest extends EditRecord
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
