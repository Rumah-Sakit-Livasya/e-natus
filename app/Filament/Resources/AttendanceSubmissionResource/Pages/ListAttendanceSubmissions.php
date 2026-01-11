<?php

namespace App\Filament\Resources\AttendanceSubmissionResource\Pages;

use App\Filament\Resources\AttendanceSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceSubmissions extends ListRecords
{
    protected static string $resource = AttendanceSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
