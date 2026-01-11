<?php

namespace App\Livewire;

use Filament\Livewire\DatabaseNotifications as BaseDatabaseNotifications;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class DatabaseNotifications extends BaseDatabaseNotifications
{
    /**
     * Override the query to show notifications from the last 7 days
     * This keeps notifications visible even after they're read,
     * giving users time to review them.
     */
    public function getNotificationsQuery(): Builder | Relation
    {
        return $this->getUser()
            ->notifications()
            ->where('data->format', 'filament')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByRaw('read_at IS NULL DESC') // Unread first
            ->orderBy('created_at', 'desc');
    }


    /**
     * Get the count of unread notifications for the badge
     */
    public function getUnreadNotificationsCountProperty(): int
    {
        return $this->getUser()
            ->unreadNotifications()
            ->where('data->format', 'filament')
            ->count();
    }
}
