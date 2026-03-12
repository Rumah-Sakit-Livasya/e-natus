<?php

namespace App\Filament\Resources\LabCheckResource\Pages;

use App\Filament\Resources\LabCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLabCheck extends EditRecord
{
    protected static string $resource = LabCheckResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $participant = Participant::find($data['participant_id'] ?? null);

        if ($participant) {
            $data['tgl_lahir'] = Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y');
            $data['usia'] = Carbon::parse($participant->date_of_birth)->age;
            $data['jenis_kelamin'] = $participant->gender;
            $data['instansi'] = $participant->department;
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
