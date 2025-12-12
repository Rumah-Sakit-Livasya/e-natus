<?php

namespace App\Livewire;

use Filament\Livewire\DatabaseNotifications as BaseDatabaseNotifications;
use Illuminate\Database\Eloquent\Collection;

class CustomDatabaseNotifications extends BaseDatabaseNotifications
{
    /**
     * Prevent auto-marking notifications as read when panel opens
     */
    public function markAllNotificationsAsRead(): void
    {
        \Log::info('CustomDatabaseNotifications: markAllNotificationsAsRead called - DOING NOTHING');
        // DO NOTHING - prevent auto-mark as read
        // Users must manually mark as read
    }
    
    /**
     * Override to show all notifications from last 30 days, not just unread
     */
    public function getNotifications(): \Illuminate\Contracts\Pagination\Paginator
    {
        $notifications = $this->getUser()
            ->notifications()
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->simplePaginate(50);
            
        \Log::info('CustomDatabaseNotifications: getNotifications called', [
            'count' => $notifications->count(),
            'user_id' => $this->getUser()->id
        ]);
        
        return $notifications;
    }
}
