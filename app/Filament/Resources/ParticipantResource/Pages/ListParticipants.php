<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use App\Exports\MedicalCheckTemplateExport;
use App\Exports\ParticipantTemplateExport;
use App\Imports\MedicalCheckImport;
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
            Actions\Action::make('download_medical_check_template')
                ->label('Template Hasil Pemeriksaan')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->form([
                    Select::make('project_request_id')
                        ->label('Pilih Project')
                        ->options(\App\Models\ProjectRequest::query()->orderByDesc('created_at')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Project terbaru muncul paling atas.'),
                    Select::make('type')
                        ->label('Pilih Pemeriksaan')
                        ->options(MedicalCheckImport::typeOptions())
                        ->required()
                        ->searchable(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Download Template Hasil Pemeriksaan')
                ->modalDescription('Alur: pilih project, pilih pemeriksaan, download template, isi hasil, lalu import kembali.')
                ->modalSubmitActionLabel('Download Template')
                ->action(function (array $data) {
                    try {
                        $projectId = (int) $data['project_request_id'];
                        $type = (string) $data['type'];
                        $filename = 'template_hasil_' . $type . '_project_' . $projectId . '_' . date('Y-m-d_H-i-s') . '.xlsx';
                        $filepath = storage_path('app/public/' . $filename);

                        Excel::store(new MedicalCheckTemplateExport($projectId, $type), $filename, 'public');

                        return response()->download($filepath)->deleteFileAfterSend(true);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Download Template Gagal')
                            ->body('Terjadi kesalahan saat download template: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('import_medical_check_result')
                ->label('Import Hasil Pemeriksaan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->form([
                    Select::make('project_request_id')
                        ->label('Pilih Project')
                        ->options(\App\Models\ProjectRequest::query()->orderByDesc('created_at')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Wajib dipilih untuk memudahkan pencocokan participant.'),
                    Select::make('type')
                        ->label('Pilih Pemeriksaan')
                        ->options(MedicalCheckImport::typeOptions())
                        ->required()
                        ->searchable(),
                    FileUpload::make('file')
                        ->label('File Excel Hasil Pemeriksaan')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->helperText('Gunakan file hasil dari template yang sudah diisi.')
                        ->maxSize(10240),
                ])
                ->action(function (array $data) {
                    try {
                        $filePath = Storage::disk('public')->path($data['file']);
                        $projectId = (int) $data['project_request_id'];
                        $type = (string) $data['type'];

                        $importer = MedicalCheckImport::fromType($type, $projectId);
                        Excel::import($importer, $filePath);

                        Storage::disk('public')->delete($data['file']);

                        Notification::make()
                            ->title('Import Hasil Berhasil')
                            ->body($importer->processedRows > 0
                                ? "{$importer->getCheckLabel()} berhasil diproses: {$importer->processedRows} baris."
                                : "Import {$importer->getCheckLabel()} selesai, tetapi tidak ada baris data yang diproses.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        if (isset($data['file'])) {
                            Storage::disk('public')->delete($data['file']);
                        }

                        Notification::make()
                            ->title('Import Hasil Gagal')
                            ->body('Terjadi kesalahan saat import hasil pemeriksaan: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
