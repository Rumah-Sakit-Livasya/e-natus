<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectRequest extends CreateRecord
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }

    // Notification logic removed from here
    // Notifications are sent from ProjectRequest model's created event
    // to avoid duplicate notifications
}
