<?php

namespace App\Filament\Resources\RontgenCheckResource\Pages;

use App\Filament\Resources\RontgenCheckResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Participant; // <-- Tambahkan use statement ini
use Carbon\Carbon;          // <-- Tambahkan use statement ini

class CreateRontgenCheck extends CreateRecord
{
    protected static string $resource = RontgenCheckResource::class;

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
                ]);
            }
        }
    }
}
