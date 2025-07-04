<?php

namespace App\Mail;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon; // Pastikan Carbon di-import

class InternalDueDateNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProjectRequest $project;
    public string $projectUrl;
    public string $daysRemaining; // <-- TAMBAHKAN PROPERTI INI

    public function __construct(ProjectRequest $project)
    {
        $this->project = $project;
        $this->projectUrl = ProjectRequestResource::getUrl('edit', ['record' => $project->id]);

        // Logika perhitungan hari yang sudah benar
        $dueDate = Carbon::parse($this->project->due_date)->startOfDay();
        $today = Carbon::today();
        $days = $today->diffInDays($dueDate, false);

        if ($days > 0) {
            $this->daysRemaining = "Jatuh tempo dalam {$days} hari";
        } elseif ($days == 0) {
            $this->daysRemaining = 'Jatuh tempo hari ini';
        } else {
            $this->daysRemaining = 'Terlambat ' . abs($days) . ' hari';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // GANTI dari ->markdown() menjadi ->view()
        return $this->subject("[TINDAKAN DIPERLUKAN] Pembayaran Proyek: {$this->project->name}")
            ->view('emails.projects.internal-due-date-notice-html'); // Ganti nama file view
    }

    public function envelope(): Envelope
    {
        // Ubah subjek menjadi lebih informatif
        return new Envelope(
            subject: "[TINDAKAN DIPERLUKAN] Pembayaran Proyek: {$this->project->name}",
        );
    }
    // ... sisa file tetap sama ...
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.projects.internal-due-date-notice',
        );
    }
}
