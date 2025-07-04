<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectRequest;
use App\Mail\DueDateReminderMail; // Kita akan buat ini di langkah selanjutnya
use App\Mail\InternalDueDateNoticeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendDueDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:send-due-date-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders for project payments that are approaching their due date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to check for due date reminders...');

        $today = Carbon::today();
        $targetDates = [
            $today->toDateString(),
            $today->copy()->addDay()->toDateString(),
            $today->copy()->addDays(2)->toDateString(),
            $today->copy()->addDays(3)->toDateString(),
        ];

        $projectsToRemind = ProjectRequest::with('client')
            ->whereIn('status_pembayaran', ['unpaid', 'partial paid'])
            ->whereIn('due_date', $targetDates)
            ->get();

        if ($projectsToRemind->isEmpty()) {
            $this->info('No projects found needing a reminder today.');
            return 0;
        }

        // Ambil alamat email internal dari file .env
        $internalEmail = env('INTERNAL_NOTIFICATION_EMAIL');

        $this->info("Found {$projectsToRemind->count()} project(s) to remind.");

        foreach ($projectsToRemind as $project) {
            // --- KIRIM EMAIL KE KLIEN ---
            if ($project->client && $project->client->email) {
                try {
                    Mail::to($project->client->email)->send(new DueDateReminderMail($project));
                    $this->info("1. Client reminder sent for '{$project->name}' to {$project->client->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send client email for '{$project->name}'. Error: " . $e->getMessage());
                    Log::error("Client DueDateReminder Error for Project ID {$project->id}: " . $e->getMessage());
                }
            } else {
                $this->warn("Skipping client email for '{$project->name}' (ID: {$project->id}) due to missing client email.");
            }

            // --- KIRIM EMAIL KE INTERNAL ---
            if ($internalEmail) {
                try {
                    Mail::to($internalEmail)->send(new InternalDueDateNoticeMail($project));
                    $this->info("2. Internal notice sent for '{$project->name}' to {$internalEmail}");
                } catch (\Exception $e) {
                    $this->error("Failed to send internal email for '{$project->name}'. Error: " . $e->getMessage());
                    Log::error("Internal DueDateReminder Error for Project ID {$project->id}: " . $e->getMessage());
                }
            } else {
                $this->warn("Skipping internal notice because INTERNAL_NOTIFICATION_EMAIL is not set in .env file.");
            }
        }

        $this->info('Finished sending reminders.');
        return 0;
    }
}
