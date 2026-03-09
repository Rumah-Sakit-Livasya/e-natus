<?php

namespace App\Filament\Resources\RontgenCheckResource\Pages;

use App\Filament\Resources\RontgenCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\RontgenCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateRontgenCheck extends CreateRecord
{
    protected static string $resource = RontgenCheckResource::class;
    private bool $isRevisionMode = false;

    private function getDefaultTemuan(): string
    {
        return "- Apex pulmo bilateral tidak ada infiltrate\n"
            . "- Corakan bronchovasculer normal\n"
            . "- Fissura minor menebal\n"
            . "- Sinus costophrenicus lancip\n"
            . "- Diafragma licin\n"
            . "- CTR < 50%\n"
            . "- Tulang tulang baik";
    }

    /**
     * Metode mount() berjalan saat halaman pertama kali dimuat.
     * Ia akan membaca parameter 'participant_id' dari URL dan
     * mengisi form dengan data pasien yang sesuai.
     */
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
            'tgl_lahir' => Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y'),
            'usia' => Carbon::parse($participant->date_of_birth)->age,
            'jenis_kelamin' => $participant->gender,
            'instansi' => $participant->department,
            'temuan' => $this->getDefaultTemuan(),
        ]);
    }

    private function fillFormFromRevisionSource(int $revisionSourceId): void
    {
        $source = RontgenCheck::query()
            ->with('participant')
            ->find($revisionSourceId);

        if (!$source) {
            return;
        }

        $payload = Arr::except($source->getAttributes(), [
            'id',
            'no_rm',
            'no_rontgen',
            'created_at',
            'updated_at',
        ]);

        $payload['participant_id'] = $source->participant_id;
        $payload['no_rm'] = $this->generateRevisionCode((string) $source->no_rm);
        $payload['no_rontgen'] = filled($source->no_rontgen)
            ? $this->generateRevisionCode((string) $source->no_rontgen, 'no_rontgen')
            : null;

        if ($source->participant) {
            $payload['tgl_lahir'] = Carbon::parse($source->participant->date_of_birth)->translatedFormat('j F Y');
            $payload['usia'] = Carbon::parse($source->participant->date_of_birth)->age;
            $payload['jenis_kelamin'] = $source->participant->gender;
            $payload['instansi'] = $source->participant->department;
        }

        $this->form->fill($payload);
    }

    private function generateRevisionCode(string $sourceCode, string $column = 'no_rm'): string
    {
        $baseCode = preg_replace('/-R\d+$/i', '', trim($sourceCode)) ?: trim($sourceCode);
        $revisionPattern = '/^' . preg_quote($baseCode, '/') . '-R(\d+)$/i';

        $maxRevision = RontgenCheck::query()
            ->where($column, 'like', $baseCode . '-R%')
            ->pluck($column)
            ->filter()
            ->reduce(function (int $max, string $code) use ($revisionPattern): int {
                if (preg_match($revisionPattern, $code, $matches) !== 1) {
                    return $max;
                }

                return max($max, (int) $matches[1]);
            }, 0);

        $nextRevision = $maxRevision + 1;

        do {
            $candidate = $baseCode . '-R' . $nextRevision;
            $nextRevision++;
        } while (RontgenCheck::query()->where($column, $candidate)->exists());

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

    protected function getRedirectUrl(): string
    {
        return ParticipantResource::getUrl();
    }
}
