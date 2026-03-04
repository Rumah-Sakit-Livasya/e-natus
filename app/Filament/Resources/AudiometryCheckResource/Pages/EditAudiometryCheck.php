<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\AudiometryCheck;
use App\Models\McuResult;
use App\Models\Participant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAudiometryCheck extends EditRecord
{
    protected static string $resource = AudiometryCheckResource::class;

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

    protected function afterSave(): void
    {
        $this->syncMcuResultFromAudiometry($this->record);
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
