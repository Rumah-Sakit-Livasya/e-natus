<?php

namespace App\Notifications;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectRequestApprovedNotification extends Notification
{
    use Queueable;

    protected ProjectRequest $projectRequest;
    protected $approverName;

    public function __construct(ProjectRequest $projectRequest, string $approverName)
    {
        $this->projectRequest = $projectRequest;
        $this->approverName = $approverName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'format' => 'filament',
            'title' => '✅ Project Request Disetujui',
            'message' => "Project '{$this->projectRequest->name}' Anda telah disetujui oleh {$this->approverName}.",
            'url' => ProjectRequestResource::getUrl('view', ['record' => $this->projectRequest]),
            'project_id' => $this->projectRequest->id,
            'project_name' => $this->projectRequest->name,
            'approver' => $this->approverName,
        ];
    }
}
