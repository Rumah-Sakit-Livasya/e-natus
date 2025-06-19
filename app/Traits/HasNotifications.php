<?php

namespace App\Traits;

use App\Models\Notification;

trait HasNotifications
{
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function unreadNotificationCount()
    {
        return $this->unreadNotifications()->count();
    }
}
