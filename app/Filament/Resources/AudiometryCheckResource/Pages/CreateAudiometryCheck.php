<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\AudiometryCheck;
use App\Models\McuResult;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateAudiometryCheck extends CreateRecord
{
    protected static string $resource = AudiometryCheckResource::class;
    private bool $isRevisionMode = false;

    public function mount(): void
    {
        parent::mount();

        $revisionSourceId = (int) request()->query('revise_from', 0);
        if ($revisionSourceId > 0) {
            $this->isRevisionMode = true;
            $this->fillFormFromRevisionSource($revisionSourceId);

            return;
        }

        $participantId = (int) request()->query('participant_id', 0);
        if ($participantId > 0) {
            $this->fillParticipantFields($participantId);
        }
    }

    private function fillParticipantFields(int $participantId): void
    {
        $participant = Participant::find($participantId);
        if (!$participant) {
            return;
        }

        $this->form->fill([
            'participant_id' => $participant->id,
            'tanggal_lahir' => Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'),
            'umur' => Carbon::parse($participant->date_of_birth)->age,
            'jenis_kelamin' => $participant->gender,
            'instansi' => $participant->department,
        ]);
    }

    private function fillFormFromRevisionSource(int $revisionSourceId): void
    {
        $source = AudiometryCheck::query()
            ->with('participant')
            ->find($revisionSourceId);

        if (!$source) {
            return;
        }

        $payload = Arr::except($source->getAttributes(), [
            'id',
            'no_rm',
            'created_at',
            'updated_at',
        ]);

        $payload['participant_id'] = $source->participant_id;
        $payload['no_rm'] = $this->generateRevisionNoRm((string) $source->no_rm);

        if ($source->participant) {
            $payload['tanggal_lahir'] = Carbon::parse($source->participant->date_of_birth)->translatedFormat('j F Y');
            $payload['umur'] = Carbon::parse($source->participant->date_of_birth)->age;
            $payload['jenis_kelamin'] = $source->participant->gender;
            $payload['instansi'] = $source->participant->department;
        }

        $this->form->fill($payload);
    }

    private function generateRevisionNoRm(string $sourceNoRm): string
    {
        $baseNoRm = preg_replace('/-R\d+$/i', '', trim($sourceNoRm)) ?: trim($sourceNoRm);
        $revisionPattern = '/^' . preg_quote($baseNoRm, '/') . '-R(\d+)$/i';

        $maxRevision = AudiometryCheck::query()
            ->where('no_rm', 'like', $baseNoRm . '-R%')
            ->pluck('no_rm')
            ->reduce(function (int $max, string $noRm) use ($revisionPattern): int {
                if (preg_match($revisionPattern, $noRm, $matches) !== 1) {
                    return $max;
                }

                return max($max, (int) $matches[1]);
            }, 0);

        $nextRevision = $maxRevision + 1;

        do {
            $candidate = $baseNoRm . '-R' . $nextRevision;
            $nextRevision++;
        } while (AudiometryCheck::query()->where('no_rm', $candidate)->exists());

        return $candidate;
    }

    protected function getFormActions(): array
    {
        if ($this->isRevisionMode) {
            return [
                $this->getCreateFormAction()->label('Simpan Revisi'),
            ];
        }

        return parent::getFormActions();
    }

    public static function canCreateAnother(): bool
    {
        return false;
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
