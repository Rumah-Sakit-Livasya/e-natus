<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\AudiometryCheck;
use App\Models\McuResult;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Participant; // <-- Tambahkan use statement ini
use Carbon\Carbon;          // <-- Tambahkan use statement ini

class CreateAudiometryCheck extends CreateRecord
{
    protected static string $resource = AudiometryCheckResource::class;

    public function mount(): void
    {
        parent::mount(); // Jangan hapus baris ini

        // Cek apakah ada 'participant_id' di query string URL
        if (request()->has('participant_id')) {
            $participantId = request()->query('participant_id');
            $participant = Participant::find($participantId);

            // Jika peserta ditemukan, isi form dengan datanya
            if ($participant) {
                $this->form->fill([
                    'participant_id' => $participant->id,
                    'tanggal_lahir'  => Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'),
                    'umur'           => Carbon::parse($participant->date_of_birth)->age,
                    'jenis_kelamin'  => $participant->gender,
                    'instansi'       => $participant->department, // Asumsi 'instansi' ada di kolom 'department'
                ]);
            }
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            ParticipantResource::getUrl() => 'Participants',
        ];
    }

    protected function afterCreate(): void
    {
        $this->syncMcuResultFromAudiometry($this->record);
    }

    protected function getRedirectUrl(): string
    {
        return ParticipantResource::getUrl();
    }

    private function syncMcuResultFromAudiometry(AudiometryCheck $audiometry): void
    {
        $participant = Participant::query()
            ->with('projectRequest')
            ->find($audiometry->participant_id);

        if (!$participant || !$participant->project_request_id) {
            return;
        }

        McuResult::updateOrCreate(
            ['participant_id' => $participant->id],
            [
                'project_request_id' => $participant->project_request_id,
                'no_mcu' => 'MCU/AUD/' . str_pad((string) $participant->id, 4, '0', STR_PAD_LEFT),
                'tanggal_mcu' => $audiometry->tanggal_pemeriksaan ?? now()->toDateString(),
            ]
        );
    }
}
