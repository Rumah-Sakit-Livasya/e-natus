<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class NotificationDebug extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    protected static string $view = 'filament.pages.notification-debug';
    
    protected static ?string $title = 'Notification Debug';
    
    protected static ?int $navigationSort = 9999;

    public function getUnreadNotificationsCount()
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function getAllNotificationsCount()
    {
        return auth()->user()->notifications()->count();
    }

    public function getNotifications()
    {
        return auth()->user()->notifications()->latest()->take(10)->get();
    }
}
