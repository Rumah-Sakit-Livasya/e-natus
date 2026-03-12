<?php

namespace App\Filament\Resources\SpirometryCheckResource\Pages;

use App\Filament\Resources\SpirometryCheckResource;
use App\Filament\Resources\ParticipantResource;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpirometryCheck extends EditRecord
{
    protected static string $resource = SpirometryCheckResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $participant = Participant::find($data['participant_id'] ?? null);

        if ($participant) {
            $data['tgl_lahir'] = Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y');
            $data['usia'] = Carbon::parse($participant->date_of_birth)->age;
            $data['jenis_kelamin'] = $participant->gender;
            $data['instansi'] = $participant->department;
        }

        $vcNilai = (float) ($data['vc_nilai'] ?? 0);
        $vcPrediksi = (float) ($data['vc_prediksi'] ?? 0);
        $fvcNilai = (float) ($data['fvc_nilai'] ?? 0);
        $fvcPrediksi = (float) ($data['fvc_prediksi'] ?? 0);
        $fev1Nilai = (float) ($data['fev1_nilai'] ?? 0);
        $fev1Prediksi = (float) ($data['fev1_prediksi'] ?? 0);

        if (blank($data['vc_percent'] ?? null)) {
            $data['vc_percent'] = $vcPrediksi > 0 ? number_format(($vcNilai / $vcPrediksi) * 100, 2, '.', '') : '0.00';
        }
        if (blank($data['fvc_percent'] ?? null)) {
            $data['fvc_percent'] = $fvcPrediksi > 0 ? number_format(($fvcNilai / $fvcPrediksi) * 100, 2, '.', '') : '0.00';
        }
        if (blank($data['fev1_percent'] ?? null)) {
            $data['fev1_percent'] = $fev1Prediksi > 0 ? number_format(($fev1Nilai / $fev1Prediksi) * 100, 2, '.', '') : '0.00';
        }

        $fev1FvcNilai = $fvcNilai > 0 ? ($fev1Nilai / $fvcNilai) * 100 : 0;
        $fev1FvcPrediksi = $fvcPrediksi > 0 ? ($fev1Prediksi / $fvcPrediksi) * 100 : 100;

        if (blank($data['fev1_fvc_nilai'] ?? null)) {
            $data['fev1_fvc_nilai'] = number_format($fev1FvcNilai, 2, '.', '');
        }
        if (blank($data['fev1_fvc_prediksi'] ?? null)) {
            $data['fev1_fvc_prediksi'] = number_format($fev1FvcPrediksi, 2, '.', '');
        }
        if (blank($data['fev1_fvc_percent'] ?? null)) {
            $data['fev1_fvc_percent'] = $fev1FvcPrediksi > 0
                ? number_format(($fev1FvcNilai / $fev1FvcPrediksi) * 100, 2, '.', '')
                : '0.00';
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
