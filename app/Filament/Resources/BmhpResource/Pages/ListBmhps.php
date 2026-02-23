<?php

namespace App\Filament\Resources\BmhpResource\Pages;

use App\Filament\Resources\BmhpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BmhpImport;
use App\Exports\BmhpTemplateExport;
use Filament\Notifications\Notification;

class ListBmhps extends ListRecords
{
    protected static string $resource = BmhpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    try {
                        $filename = 'bmhp_template_' . date('Y-m-d_H-i-s') . '.xlsx';
                        $filepath = storage_path('app/public/' . $filename);

                        Excel::store(new BmhpTemplateExport(), $filename, 'public');

                        return response()->download($filepath)->deleteFileAfterSend(true);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Download Gagal')
                            ->body('Terjadi kesalahan saat download template: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('import_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->helperText('Pilih file Excel (.xlsx atau .xls) yang berisi data BMHP')
                        ->maxSize(10240), // 10MB
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = Storage::disk('public')->path($data['file']);

                        Excel::import(new BmhpImport(), $filePath);

                        // Hapus file setelah import
                        Storage::disk('public')->delete($data['file']);

                        Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data BMHP berhasil diimport dari file Excel')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Hapus file jika terjadi error
                        if (isset($data['file'])) {
                            Storage::disk('public')->delete($data['file']);
                        }

                        Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan saat import: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
