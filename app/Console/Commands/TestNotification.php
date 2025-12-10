<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\ProjectRequestCreated;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TestNotification extends Command
{
    protected $signature = 'test:notification {user_id}';
    protected $description = 'Send a test notification to a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        // Send notification using DatabaseMessage
        $user->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable): array
            {
                return ['database'];
            }

            public function toDatabase($notifiable): DatabaseMessage
            {
                return new DatabaseMessage([
                    'title' => 'Test Notification',
                    'message' => 'This is a test notification sent at ' . now()->format('H:i:s'),
                    'url' => '/dashboard',
                ]);
            }
        });

        $this->info("Test notification sent to {$user->name} (ID: {$userId})");
        $this->info("Total unread notifications: {$user->unreadNotifications()->count()}");
        
        return 0;
    }
}
