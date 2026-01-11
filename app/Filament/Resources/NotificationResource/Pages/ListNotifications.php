<?php
// File: app/Filament/Resources/NotificationResource/Pages/ListNotifications.php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAllAsRead')
                ->label('Tandai Semua sebagai Dibaca')
                ->icon('heroicon-o-check-badge')
                ->action(function () {
                    // --- PERBAIKAN DI SINI ---
                    // 1. Bangun query untuk notifikasi yang belum dibaca.
                    // 2. Gunakan ->get() untuk mengeksekusinya dan mendapatkan koleksi.
                    $unreadNotifications = auth()->user()->notifications()->whereNull('read_at')->get();

                    // 3. Jika koleksi tidak kosong, iterasi dan tandai.
                    if ($unreadNotifications->isNotEmpty()) {
                        // 'each->markAsRead()' sekarang pasti berhasil karena kita bekerja pada koleksi.
                        $unreadNotifications->each->markAsRead();
                    }

                    Notification::make()->title('Semua notifikasi telah ditandai sebagai sudah dibaca')->success()->send();
                })
                ->visible(function (): bool {
                    // --- DAN PERBAIKAN DI SINI ---
                    // Logika yang sama: bangun query secara eksplisit lalu cek 'exists()'.
                    return auth()->user()->notifications()->whereNull('read_at')->exists();
                }),
        ];
    }
}
