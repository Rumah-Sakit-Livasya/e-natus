<?php

namespace App\Filament\Resources\UsgMammaeCheckResource\Pages;

use App\Filament\Resources\UsgMammaeCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Participant; // <-- Tambahkan use statement ini
use Carbon\Carbon;          // <-- Tambahkan use statement ini

class CreateUsgMammaeCheck extends CreateRecord
{
    protected static string $resource = UsgMammaeCheckResource::class;

    private function getDefaultTemuan(): string
    {
        return "- Tampak parenkim mammae dominan glandular\n"
            . "- Tak tampak lesi massa/kistik\n"
            . "- Kutis dan subkutis normal\n"
            . "- Tak tampak retraksi papilla mammae kanan\n"
            . "- Tak tampak kalsifikasi";
    }

    /**
     * Metode mount() berjalan saat halaman pertama kali dimuat.
     * Ia akan membaca parameter 'participant_id' dari URL dan
     * mengisi form dengan data pasien yang sesuai.
     */
    public function mount(): void
    {
        parent::mount();

        if (request()->has('participant_id')) {
            $participantId = request()->query('participant_id');
            $participant = Participant::find($participantId);

            if ($participant) {
                $this->form->fill([
                    'participant_id' => $participant->id,
                    'tgl_lahir'      => Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'),
                    'usia'           => Carbon::parse($participant->date_of_birth)->age,
                    'jenis_kelamin'  => $participant->gender,
                    'instansi'       => $participant->department,
                    'nik_no_pekerja' => $participant->employee_code ?: '-/-',
                    'mammae_kanan'   => $this->getDefaultTemuan(),
                    'mammae_kiri'    => str_replace('kanan', 'kiri', $this->getDefaultTemuan()),
                    'catatan_tambahan' => 'Tak tampak limfadenopathy axilla bilateral',
                    'kesimpulan'     => "1. Mammae kanan kiri tak tampak kelainan (Negative Finding-BIRADS 1)\n2. Tak tampak limfadenopathy axilla bilateral",
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

    protected function getRedirectUrl(): string
    {
        return ParticipantResource::getUrl();
    }
}
