<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Notifications\DatabaseNotification;

class ViewNotification extends ViewRecord
{
    public static string $resource = NotificationResource::class;

    public function getHeaderActions(): array
    {
        return [
            // ==========================================================
            // ▼▼▼ TOMBOL AKSI DI HALAMAN VIEW/DETAIL ▼▼▼
            // ==========================================================
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (DatabaseNotification $record) {
                    // Panggil method static dari resource
                    NotificationResource::handleApproval($record, 'approved');
                    // Refresh data di halaman agar tombol hilang
                    $this->refreshFormData([]);
                })
                // Gunakan method static dari resource untuk visibility
                ->visible(fn(DatabaseNotification $record): bool => NotificationResource::isActionable($record)),

            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (DatabaseNotification $record) {
                    NotificationResource::handleApproval($record, 'rejected');
                    $this->refreshFormData([]);
                })
                ->visible(fn(DatabaseNotification $record): bool => NotificationResource::isActionable($record)),
        ];
    }
}
