<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use App\Exports\ParticipantTemplateExport;
use App\Imports\ParticipantImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Select::make('project_request_id')
                        ->label('Project untuk Export')
                        ->options(\App\Models\ProjectRequest::query()->orderByDesc('created_at')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Export hanya data participant dari project yang dipilih.'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Export Data Participants')
                ->modalDescription(new HtmlString(
                    "<div style='text-align:left'>"
                        . "<p style='margin-bottom:8px'>File export bisa dipakai untuk <strong>edit</strong> dan <strong>create</strong> data:</p>"
                        . "<ol style='margin:0;padding-left:18px'>"
                        . "<li><strong>Edit data existing:</strong> jangan ubah kolom <code>ID</code>, cukup ubah kolom lainnya.</li>"
                        . "<li><strong>Tambah data baru:</strong> isi baris baru dan kosongkan kolom <code>ID</code>.</li>"
                        . "<li><strong>Simpan file</strong>, lalu import kembali lewat tombol <strong>Import Excel</strong>.</li>"
                        . "</ol>"
                        . "</div>"
                ))
                ->modalSubmitActionLabel('Lanjut Export')
                ->modalCancelActionLabel('Batal')
                ->action(function (array $data) {
                    try {
                        $projectId = (int) $data['project_request_id'];
                        $filename = 'participant_project_' . $projectId . '_' . date('Y-m-d_H-i-s') . '.xlsx';
                        $filepath = storage_path('app/public/' . $filename);

                        Excel::store(new ParticipantTemplateExport($projectId), $filename, 'public');

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
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Select::make('project_request_id')
                        ->label('Project Tujuan Import')
                        ->options(\App\Models\ProjectRequest::query()->orderByDesc('created_at')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Semua baris di file akan diimport ke project ini.'),
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->helperText('Baris ke-2 adalah contoh dan akan di-skip. Isi data mulai baris ke-3.')
                        ->maxSize(10240),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = Storage::disk('public')->path($data['file']);

                        $importer = new ParticipantImport((int) $data['project_request_id']);
                        Excel::import($importer, $filePath);

                        Storage::disk('public')->delete($data['file']);

                        $processed = $importer->processedRows;
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body($processed > 0
                                ? "Data participants berhasil diproses: {$processed} baris."
                                : 'Import selesai, tetapi tidak ada baris data yang diproses. Isi data mulai baris ke-3.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
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
