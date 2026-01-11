<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
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
}
