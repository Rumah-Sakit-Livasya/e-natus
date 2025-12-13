<?php

namespace App\Notifications;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectRequestLevel2Approval extends Notification
{
    use Queueable;

    protected ProjectRequest $projectRequest;

    public function __construct(ProjectRequest $projectRequest)
    {
        $this->projectRequest = $projectRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $userName = $this->projectRequest->user?->name ?? 'Sistem';
        $level1Approver = $this->projectRequest->approvalLevel1By?->name ?? 'Level 1';

        return new DatabaseMessage([
            'format' => 'filament',
            'title' => 'Persetujuan Level 2 Diperlukan',
            'message' => "Proyek '{$this->projectRequest->name}' oleh {$userName} telah disetujui {$level1Approver} (Level 1). Membutuhkan persetujuan Level 2 Anda.",
            'url' => ProjectRequestResource::getUrl('edit', ['record' => $this->projectRequest]),
            'is_approvable' => true,
            'record_model' => ProjectRequest::class,
            'record_id' => $this->projectRequest->id,
            'approval_level' => 2, // Indicate this is Level 2 approval
        ]);
    }
}
