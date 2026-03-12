<?php

namespace App\Filament\Resources\DrugTestResource\Pages;

use App\Filament\Resources\DrugTestResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDrugTest extends EditRecord
{
    protected static string $resource = DrugTestResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $participant = Participant::find($data['participant_id'] ?? null);

        if ($participant) {
            $data['nik'] = $participant->employee_code;
            $data['department'] = $participant->department;
            $data['tgl_lahir'] = Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y');
            $data['umur'] = Carbon::parse($participant->date_of_birth)->age;
            $data['j_kel'] = $participant->gender;
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
