<?php

namespace App\Filament\Resources\UsgMammaeCheckResource\Pages;

use App\Filament\Resources\UsgMammaeCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsgMammaeCheck extends EditRecord
{
    protected static string $resource = UsgMammaeCheckResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $participant = Participant::find($data['participant_id'] ?? null);

        if ($participant) {
            $data['tgl_lahir'] = Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y');
            $data['usia'] = Carbon::parse($participant->date_of_birth)->age;
            $data['jenis_kelamin'] = $participant->gender;
            $data['instansi'] = $participant->department;
        }

        if (! empty($data['gambar_hasil_usg_lampiran']) && is_array($data['gambar_hasil_usg_lampiran'])) {
            return $data;
        }

        $legacyImages = collect([
            $data['gambar_hasil_usg'] ?? null,
            $data['gambar_hasil_usg_2'] ?? null,
            $data['gambar_hasil_usg_3'] ?? null,
            $data['gambar_hasil_usg_4'] ?? null,
            $data['gambar_hasil_usg_5'] ?? null,
            $data['gambar_hasil_usg_6'] ?? null,
        ])->filter()->values()->all();

        if ($legacyImages !== []) {
            $data['gambar_hasil_usg_lampiran'] = $legacyImages;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            ParticipantResource::getUrl() => 'Participants',
        ];
    }
}
