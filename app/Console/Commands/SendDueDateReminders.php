<?php

namespace App\Console\Commands;

// Import berbagai class yang dibutuhkan
use Illuminate\Console\Command;
use App\Models\ProjectRequest;
use App\Mail\DueDateReminderMail; // Mail untuk klien
use App\Mail\InternalDueDateNoticeMail; // Mail untuk internal
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDueDateReminders extends Command
{
    // Nama perintah artisan yang bisa dijalankan di terminal
    protected $signature = 'project:send-due-date-reminders';

    // Deskripsi singkat tentang perintah ini
    protected $description = 'Sends email reminders for project payments that are approaching their due date.';

    /**
     * Fungsi utama yang akan dijalankan saat perintah dipanggil.
     */
    public function handle()
    {
        // 1. Tampilkan pesan bahwa proses pengecekan dimulai
        $this->info('Starting to check for due date reminders...');

        // 2. Tentukan tanggal-tanggal target (hari ini, +1, +2, +3 hari)
        $today = Carbon::today();
        $targetDates = [
            $today->toDateString(),
            $today->copy()->addDay()->toDateString(),
            $today->copy()->addDays(2)->toDateString(),
            $today->copy()->addDays(3)->toDateString(),
        ];

        // 3. Ambil daftar project yang status pembayarannya 'unpaid' atau 'partial paid' dan due_date-nya masuk dalam targetDates
        $projectsToRemind = ProjectRequest::with('client')
            ->whereIn('status_pembayaran', ['unpaid', 'partial paid'])
            ->whereIn('due_date', $targetDates)
            ->get();

        // 4. Jika tidak ada project yang perlu diingatkan, tampilkan pesan dan hentikan proses
        if ($projectsToRemind->isEmpty()) {
            $this->info('No projects found needing a reminder today.');
            return 0;
        }

        // 5. Ambil alamat email internal dari file .env (INTERNAL_NOTIFICATION_EMAIL)
        $internalEmail = env('INTERNAL_NOTIFICATION_EMAIL');

        // 6. Tampilkan jumlah project yang akan diingatkan
        $this->info("Found {$projectsToRemind->count()} project(s) to remind.");

        // 7. Loop setiap project yang perlu diingatkan
        foreach ($projectsToRemind as $project) {
            // --- Bagian A: Kirim email ke klien ---
            if ($project->client && $project->client->email) {
                try {
                    // Kirim email pengingat ke email klien
                    Mail::to($project->client->email)->send(new DueDateReminderMail($project));
                    $this->info("1. Client reminder sent for '{$project->name}' to {$project->client->email}");
                } catch (\Exception $e) {
                    // Jika gagal, tampilkan error dan log pesan error-nya
                    $this->error("Failed to send client email for '{$project->name}'. Error: " . $e->getMessage());
                    Log::error("Client DueDateReminder Error for Project ID {$project->id}: " . $e->getMessage());
                }
            } else {
                // Jika tidak ada email klien, tampilkan peringatan
                $this->warn("Skipping client email for '{$project->name}' (ID: {$project->id}) due to missing client email.");
            }

            // --- Bagian B: Kirim email ke internal ---
            if ($internalEmail) {
                try {
                    // Kirim email notifikasi internal
                    Mail::to($internalEmail)->send(new InternalDueDateNoticeMail($project));
                    $this->info("2. Internal notice sent for '{$project->name}' to {$internalEmail}");
                } catch (\Exception $e) {
                    // Jika gagal, tampilkan error dan log pesan error-nya
                    $this->error("Failed to send internal email for '{$project->name}'. Error: " . $e->getMessage());
                    Log::error("Internal DueDateReminder Error for Project ID {$project->id}: " . $e->getMessage());
                }
            } else {
                // Jika email internal tidak di-set di .env, tampilkan peringatan
                $this->warn("Skipping internal notice because INTERNAL_NOTIFICATION_EMAIL is not set in .env file.");
            }
        }

        // 8. Tampilkan pesan bahwa proses pengiriman selesai
        $this->info('Finished sending reminders.');
        return 0;
    }
}
