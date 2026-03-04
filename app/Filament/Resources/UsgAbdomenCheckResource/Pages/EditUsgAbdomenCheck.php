<?php

namespace App\Filament\Resources\UsgAbdomenCheckResource\Pages;

use App\Filament\Resources\UsgAbdomenCheckResource;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsgAbdomenCheck extends EditRecord
{
    protected static string $resource = UsgAbdomenCheckResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['gambar_hasil_usg_lampiran']) && is_array($data['gambar_hasil_usg_lampiran'])) {
            return $data;
        }

        $legacyImages = collect([
            $data['gambar_hasil_usg'] ?? null,
            $data['gambar_hasil_usg_2'] ?? null,
            $data['gambar_hasil_usg_3'] ?? null,
            $data['gambar_hasil_usg_4'] ?? null,
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
