<?php

namespace Database\Seeders;

use App\Models\AudiometryCheck;
use App\Models\McuResult;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class BackfillMcuResultFromAudiometrySeeder extends Seeder
{
    public function run(): void
    {
        AudiometryCheck::query()
            ->orderBy('id')
            ->chunk(200, function ($checks): void {
                foreach ($checks as $check) {
                    $participant = Participant::query()->find($check->participant_id);

                    if (!$participant || !$participant->project_request_id) {
                        continue;
                    }

                    McuResult::updateOrCreate(
                        ['participant_id' => $participant->id],
                        [
                            'project_request_id' => $participant->project_request_id,
                            'no_mcu' => 'MCU/AUD/' . str_pad((string) $participant->id, 4, '0', STR_PAD_LEFT),
                            'tanggal_mcu' => $check->tanggal_pemeriksaan ?? now()->toDateString(),
                        ]
                    );
                }
            });
    }
}
