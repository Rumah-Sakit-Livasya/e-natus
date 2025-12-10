<?php

namespace App\Notifications;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ProjectRequestLevel2Approval extends Notification
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
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        $level1Approver = $this->projectRequest->approvalLevel1By?->name ?? 'Sistem';

        return new DatabaseMessage([
            'format' => 'filament',
            'title' => 'Persetujuan Level 2 Diperlukan',
            'message' => "Proyek '{$this->projectRequest->name}' telah disetujui Level 1 oleh {$level1Approver}. Persetujuan Level 2 Anda diperlukan.",
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
