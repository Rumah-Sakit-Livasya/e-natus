<?php

namespace App\Mail;

use App\Models\ProjectRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Carbon tidak lagi dibutuhkan di sini jika semua format tanggal ada di view
// use Carbon\Carbon;

class DueDateReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProjectRequest $project;

    /**
     * Create a new message instance.
     */
    public function __construct(ProjectRequest $project)
    {
        $this->project = $project;
    }

    /**
     * Get the message envelope.
     * (Tidak ada perubahan di sini, sudah benar)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Pembayaran Proyek: ' . $this->project->name,
        );
    }

    /**
     * Get the message content definition.
     * --- INI BAGIAN UTAMA YANG DIPERBAIKI ---
     */
    public function content(): Content
    {
        // Kita mengubah 'markdown:' menjadi 'view:' untuk menggunakan template HTML biasa
        return new Content(
            view: 'emails.projects.due-date-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     * (Tidak ada perubahan di sini)
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
