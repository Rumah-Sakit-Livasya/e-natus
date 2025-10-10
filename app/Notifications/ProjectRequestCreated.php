<?php

namespace App\Notifications;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectRequestCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ProjectRequest $projectRequest)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database']; // atau tambahkan 'mail' jika ingin kirim email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        $userName = $this->projectRequest->user?->name ?? 'Sistem';

        return new DatabaseMessage([
            'title' => 'Persetujuan Proyek Baru',
            'message' => "Proyek '{$this->projectRequest->name}' oleh {$userName} membutuhkan persetujuan.",

            // ==========================================================
            // ▼▼▼ PERBAIKAN DI SINI ▼▼▼
            // ==========================================================
            'url' => ProjectRequestResource::getUrl('edit', ['record' => $this->projectRequest]),

            'is_approvable' => true,
            'record_model' => ProjectRequest::class,
            'record_id' => $this->projectRequest->id,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
