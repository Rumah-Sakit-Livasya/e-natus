<?php

namespace App\Filament\Resources\AttendanceSubmissionResource\Pages;

use App\Filament\Resources\AttendanceSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceSubmission extends EditRecord
{
    protected static string $resource = AttendanceSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
