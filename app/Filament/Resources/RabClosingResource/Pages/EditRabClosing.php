<?php

namespace App\Filament\Resources\RabClosingResource\Pages;

use App\Filament\Resources\RabClosingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditRabClosing extends EditRecord
{
    protected static string $resource = RabClosingResource::class;

    protected function getHeaderActions(): array
    {
        // Kode header actions Anda biarkan seperti apa adanya, sudah benar.
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('print_finance')
                    ->label('Preview Laporan Keuangan')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->url(fn() => route('rab-closing.print', ['record' => $this->record->id, 'view' => 'finance']), shouldOpenInNewTab: true),
                Actions\Action::make('print_justification')
                    ->label('Preview Laporan Justifikasi')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->url(fn() => route('rab-closing.print', ['record' => $this->record->id, 'view' => 'justification']), shouldOpenInNewTab: true),
            ])
                ->label('Print Preview')
                ->icon('heroicon-o-printer')
                ->button(),
            Actions\Action::make('finalize')
                ->label('Finalisasi RAB')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->modalHeading('Finalisasi RAB Closing')
                ->modalSubheading('Apakah Anda yakin ingin menyelesaikan RAB ini? Setelah difinalisasi, data tidak bisa diubah lagi.')
                ->modalButton('Ya, Finalisasi')
                ->action(function () {
                    DB::transaction(function () {
                        $this->record->update(['status' => 'final']);

                        // Kembalikan aset menjadi available
                        $assetIds = $this->record->projectRequest->asset_ids ?? [];
                        if (!empty($assetIds)) {
                            \App\Models\Aset::whereIn('id', $assetIds)->update(['status' => 'available']);
                        }
                    });

                    Notification::make()->title('RAB berhasil difinalisasi dan aset telah dikembalikan')->success()->send();
                    return redirect(RabClosingResource::getUrl('edit', ['record' => $this->record]));
                })->visible(fn(): bool => $this->record->status === 'draft'),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Hook untuk MEMUAT DATA SECARA MANUAL ke dalam repeater.
     * Ini berjalan sebelum form ditampilkan ke pengguna.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 1. Eager load relasi untuk efisiensi
        $this->record->loadMissing('operasionalItems.attachments', 'feePetugasItems.attachments');

        // 2. Ubah data relasi menjadi format yang bisa dibaca oleh Repeater
        $data['operasionalItems'] = $this->record->operasionalItems->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'price' => $item->price,
                'attachments' => $item->attachments->pluck('file_path')->toArray(),
            ];
        })->toArray();

        $data['feePetugasItems'] = $this->record->feePetugasItems->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $item->description,
                'price' => $item->price,
                'attachments' => $item->attachments->pluck('file_path')->toArray(),
            ];
        })->toArray();

        return $data;
    }

    /**
     * Mengambil alih proses penyimpanan default untuk menangani relasi secara manual.
     *
     * @param array $data Data yang sudah divalidasi dari form.
     * @return Model Record yang sudah diupdate.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // dd($data); // <-- TAMBAHKAN INI DI SINI
        // Gunakan database transaction untuk memastikan semua atau tidak sama sekali.
        DB::transaction(function () use ($record, $data) {

            // =============================================================
            // Bagian 1: Menangani Item Operasional
            // =============================================================
            if (isset($data['operasionalItems'])) {
                $operasionalItemsData = $data['operasionalItems'];

                // Hapus item lama yang tidak ada di data baru
                $existingOperasionalIds = collect($operasionalItemsData)->pluck('id')->filter();
                $record->operasionalItems()->whereNotIn('id', $existingOperasionalIds)->delete();

                foreach ($operasionalItemsData as $itemData) {
                    // Ambil data lampiran sebelum data utama disimpan
                    $attachmentPaths = $itemData['attachments'] ?? [];
                    unset($itemData['attachments']); // Hapus dari array utama

                    // Update atau Buat item operasional
                    $operasionalItem = $record->operasionalItems()->updateOrCreate(
                        ['id' => $itemData['id'] ?? null], // Cari berdasarkan ID, atau buat baru
                        $itemData // Data untuk di-update/dibuat
                    );

                    // Sinkronkan lampiran SETELAH item operasional disimpan
                    if ($operasionalItem) {
                        // Hapus lampiran lama untuk item ini
                        $operasionalItem->attachments()->delete();

                        // Buat data baru untuk 'createMany'
                        $newAttachments = collect($attachmentPaths)->map(function ($path) {
                            return ['file_path' => $path];
                        })->all();

                        // Buat lampiran baru jika ada
                        if (!empty($newAttachments)) {
                            $operasionalItem->attachments()->createMany($newAttachments);
                        }
                    }
                }
            }

            // =============================================================
            // Bagian 2: Menangani Item Fee Petugas (Logika yang sama)
            // =============================================================
            if (isset($data['feePetugasItems'])) {
                $feeItemsData = $data['feePetugasItems'];

                $existingFeeIds = collect($feeItemsData)->pluck('id')->filter();
                $record->feePetugasItems()->whereNotIn('id', $existingFeeIds)->delete();

                foreach ($feeItemsData as $itemData) {
                    $attachmentPaths = $itemData['attachments'] ?? [];
                    unset($itemData['attachments']);

                    $feeItem = $record->feePetugasItems()->updateOrCreate(
                        ['id' => $itemData['id'] ?? null],
                        $itemData
                    );

                    if ($feeItem) {
                        $feeItem->attachments()->delete();
                        $newAttachments = collect($attachmentPaths)->map(fn($path) => ['file_path' => $path])->all();
                        if (!empty($newAttachments)) {
                            $feeItem->attachments()->createMany($newAttachments);
                        }
                    }
                }
            }

            // =============================================================
            // Bagian 3: Update record utama (RabClosing)
            // =============================================================
            // Hapus data repeater dari array utama agar tidak menyebabkan error
            unset($data['operasionalItems']);
            unset($data['feePetugasItems']);

            $record->update($data);
        });

        // Tampilkan notifikasi sukses jika berhasil
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        return $record;
    }
}
