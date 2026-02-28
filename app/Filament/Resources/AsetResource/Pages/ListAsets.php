<?php

namespace App\Filament\Resources\AsetResource\Pages;

use App\Exports\AsetDataExport;
use App\Filament\Resources\AsetResource;
use App\Filament\Imports\AsetImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListAsets extends ListRecords
{
    protected static string $resource = AsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_data')
                ->label('Export Data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Export Data Aset')
                ->modalDescription(new HtmlString(
                    "<div style='text-align:left'>"
                    . "<p style='margin-bottom:8px'>File export bisa dipakai untuk <strong>edit</strong> dan <strong>create</strong> data:</p>"
                    . "<ol style='margin:0;padding-left:18px'>"
                    . "<li><strong>Edit data existing:</strong> jangan ubah kolom <code>id</code>, ubah kolom lain yang diperlukan.</li>"
                    . "<li><strong>Tambah data baru:</strong> isi baris baru dan kosongkan kolom <code>id</code>.</li>"
                    . "<li><strong>Simpan file</strong>, lalu import kembali lewat tombol <strong>Import Excel</strong>.</li>"
                    . "</ol>"
                    . "</div>"
                ))
                ->modalSubmitActionLabel('Lanjut Export')
                ->modalCancelActionLabel('Batal')
                ->action(function () {
                    try {
                        $filename = 'aset_data_' . date('Y-m-d_H-i-s') . '.xlsx';
                        $filepath = storage_path('app/public/' . $filename);

                        Excel::store(new AsetDataExport(), $filename, 'public');

                        return response()->download($filepath)->deleteFileAfterSend(true);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Export Gagal')
                            ->body('Terjadi kesalahan saat export data aset: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            ImportAction::make()
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->importer(AsetImporter::class),

            CreateAction::make(),
        ];
    }
}
