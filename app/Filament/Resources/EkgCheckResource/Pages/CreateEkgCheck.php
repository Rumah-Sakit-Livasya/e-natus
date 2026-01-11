<?php

namespace App\Filament\Resources\EkgCheckResource\Pages;

use App\Filament\Resources\EkgCheckResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Participant; // <-- Tambahkan use statement ini
use Carbon\Carbon;          // <-- Tambahkan use statement ini

class CreateEkgCheck extends CreateRecord
{
    protected static string $resource = EkgCheckResource::class;

    /**
     * Metode mount() berjalan saat halaman pertama kali dimuat.
     * Ia akan membaca parameter 'participant_id' dari URL dan
     * mengisi form dengan data pasien yang sesuai.
     */
    public function mount(): void
    {
        parent::mount();

        // Cek jika 'participant_id' dikirim dari tombol di tabel Participant
        if (request()->has('participant_id')) {
            $participantId = request()->query('participant_id');
            $participant = Participant::find($participantId);

            // Jika peserta ditemukan, isi form
            if ($participant) {
                $this->form->fill([
                    'participant_id' => $participant->id,
                    'tgl_lahir'      => Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'),
                    'usia'           => Carbon::parse($participant->date_of_birth)->age,
                    'jenis_kelamin'  => $participant->gender,
                    'instansi'       => $participant->department, // Asumsi 'instansi' ada di kolom 'department'
                ]);
            }
        }
    }
}
