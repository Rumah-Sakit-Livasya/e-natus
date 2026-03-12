<?php

namespace App\Filament\Resources\AudiometryCheckResource\Pages;

use App\Filament\Resources\AudiometryCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\AudiometryCheck;
use App\Models\McuResult;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditAudiometryCheck extends EditRecord
{
    protected static string $resource = AudiometryCheckResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)->disabled();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $participant = Participant::find($data['participant_id'] ?? null);

        if (! $participant) {
            return $data;
        }

        $data['tanggal_lahir'] = Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y');
        $data['umur'] = Carbon::parse($participant->date_of_birth)->age;
        $data['jenis_kelamin'] = $participant->gender;
        $data['instansi'] = $participant->department;

        if (blank($data['derajat_ad'] ?? null)) {
            $data['derajat_ad'] = $this->formatAverage([
                $data['ad_ac_500'] ?? null,
                $data['ad_ac_1000'] ?? null,
                $data['ad_ac_2000'] ?? null,
                $data['ad_ac_4000'] ?? null,
            ]);
        }

        if (blank($data['derajat_as'] ?? null)) {
            $data['derajat_as'] = $this->formatAverage([
                $data['as_ac_500'] ?? null,
                $data['as_ac_1000'] ?? null,
                $data['as_ac_2000'] ?? null,
                $data['as_ac_4000'] ?? null,
            ]);
        }

        if (blank($data['derajat_ad_bc'] ?? null)) {
            $data['derajat_ad_bc'] = $this->formatAverage([
                $data['ad_bc_500'] ?? null,
                $data['ad_bc_1000'] ?? null,
                $data['ad_bc_2000'] ?? null,
                $data['ad_bc_4000'] ?? null,
            ]);
        }

        if (blank($data['derajat_as_bc'] ?? null)) {
            $data['derajat_as_bc'] = $this->formatAverage([
                $data['as_bc_500'] ?? null,
                $data['as_bc_1000'] ?? null,
                $data['as_bc_2000'] ?? null,
                $data['as_bc_4000'] ?? null,
            ]);
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

    /**
     * @param array<int, mixed> $values
     */
    private function formatAverage(array $values): string
    {
        foreach ($values as $value) {
            if ($value === null || $value === '' || ! is_numeric($value)) {
                return '-';
            }
        }

        return number_format(array_sum(array_map('floatval', $values)) / count($values), 2, '.', '');
    }
}
