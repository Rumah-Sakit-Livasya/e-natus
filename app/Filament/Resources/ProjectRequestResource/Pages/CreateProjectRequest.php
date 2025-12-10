<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\User;
use App\Notifications\ProjectRequestCreated;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectRequest extends CreateRecord
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(); // Ini akan redirect ke halaman list
    }

    /**
     * Hook yang dipanggil setelah record berhasil dibuat
     */
    protected function afterCreate(): void
    {
        // Kirim notifikasi ke user dengan permission 'approve_project_level_1'
        $approvers = User::permission('approve_project_level_1')->get();
        
        foreach ($approvers as $approver) {
            $approver->notify(new ProjectRequestCreated($this->record));
        }
    }
}
