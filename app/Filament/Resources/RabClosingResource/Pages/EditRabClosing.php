<?php

namespace App\Filament\Resources\RabClosingResource\Pages;

use App\Filament\Resources\RabClosingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model; // <-- Pastikan ini ada

class EditRabClosing extends EditRecord
{
    protected static string $resource = RabClosingResource::class;

    protected function getHeaderActions(): array
    {
        // Kode header actions Anda sudah benar.
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('print_finance')->label('Preview Laporan Keuangan')->icon('heroicon-o-currency-dollar')->color('info')->url(fn() => route('rab-closing.print', ['record' => $this->record->id, 'view' => 'finance']), shouldOpenInNewTab: true),
                Actions\Action::make('print_justification')->label('Preview Laporan Justifikasi')->icon('heroicon-o-document-text')->color('warning')->url(fn() => route('rab-closing.print', ['record' => $this->record->id, 'view' => 'justification']), shouldOpenInNewTab: true),
            ])->label('Print Preview')->icon('heroicon-o-printer')->button(),
            Actions\Action::make('finalize')->label('Finalisasi RAB')->color('success')->icon('heroicon-o-check-badge')->requiresConfirmation()->modalHeading('Finalisasi RAB Closing')->modalSubheading('Apakah Anda yakin ingin menyelesaikan RAB ini? Setelah difinalisasi, data tidak bisa diubah lagi.')->modalButton('Ya, Finalisasi')->action(function () {
                $this->record->update(['status' => 'final']);
                Notification::make()->title('RAB berhasil difinalisasi')->success()->send();
                return redirect(RabClosingResource::getUrl('edit', ['record' => $this->record]));
            })->visible(fn(): bool => $this->record->status === 'draft'),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Hook yang berjalan SEBELUM data ditampilkan di form.
     * Kode ini sudah benar.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing('operasionalItems.attachments', 'feePetugasItems.attachments');
        if ($this->record->operasionalItems) {
            foreach ($this->record->operasionalItems as $index => $item) {
                $data['operasionalItems'][$index]['attachments_upload'] = $item->attachments->pluck('file_path')->toArray();
            }
        }
        if ($this->record->feePetugasItems) {
            foreach ($this->record->feePetugasItems as $index => $item) {
                $data['feePetugasItems'][$index]['attachments_upload'] = $item->attachments->pluck('file_path')->toArray();
            }
        }
        return $data;
    }

    // ===================================================================
    // KITA HAPUS SEMUA HOOK LAMA (mutateFormDataBeforeSave dan afterSave)
    // DAN GANTI DENGAN SATU METHOD KOKOH DI BAWAH INI
    // ===================================================================

    /**
     * Mengambil alih proses penyimpanan default untuk menangani lampiran secara manual.
     *
     * @param array $data Data yang sudah divalidasi dari form.
     * @return Model Record yang sudah diupdate.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // 1. Siapkan variabel untuk menyimpan data lampiran
        $operasionalAttachmentsToSync = [];
        if (isset($data['operasionalItems'])) {
            foreach ($data['operasionalItems'] as $key => $itemData) {
                // Simpan data lampiran dan ID itemnya
                $operasionalAttachmentsToSync[$key] = $itemData['attachments_upload'];
                // Hapus data lampiran dari array utama agar tidak menyebabkan error
                unset($data['operasionalItems'][$key]['attachments_upload']);
            }
        }

        $feeAttachmentsToSync = [];
        if (isset($data['feePetugasItems'])) {
            foreach ($data['feePetugasItems'] as $key => $itemData) {
                $feeAttachmentsToSync[$key] = $itemData['attachments_upload'];
                unset($data['feePetugasItems'][$key]['attachments_upload']);
            }
        }

        // 2. Lakukan penyimpanan utama (tanpa data lampiran)
        $record->update($data);

        // 3. Sekarang, sinkronkan lampiran
        // Muat ulang relasi untuk memastikan kita punya ID dari item yang baru dibuat
        $record->load('operasionalItems', 'feePetugasItems');

        $this->syncAttachments($record->operasionalItems, $operasionalAttachmentsToSync);
        $this->syncAttachments($record->feePetugasItems, $feeAttachmentsToSync);

        return $record;
    }

    /**
     * Method helper untuk menyinkronkan lampiran.
     */
    protected function syncAttachments($savedItems, array $attachmentsData): void
    {
        // Reset keys untuk pencocokan berdasarkan urutan yang andal
        $savedItems = $savedItems->values();
        $attachmentsData = array_values($attachmentsData);

        foreach ($attachmentsData as $index => $files) {
            $itemModel = $savedItems->get($index);

            if ($itemModel) {
                // Hapus lampiran lama
                $itemModel->attachments()->delete();

                // Siapkan data lampiran baru
                $attachmentsToCreate = [];
                if (is_array($files)) {
                    foreach ($files as $filePath) {
                        $attachmentsToCreate[] = ['file_path' => $filePath];
                    }
                }

                // Simpan lampiran baru
                if (!empty($attachmentsToCreate)) {
                    $itemModel->attachments()->createMany($attachmentsToCreate);
                }
            }
        }
    }
}
