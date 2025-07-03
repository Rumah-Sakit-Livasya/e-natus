<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Request;

class ManageProjectAttendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.manage-project-attendance';
    protected static bool $shouldRegisterNavigation = false; // Sembunyikan dari navigasi utama

    public ProjectRequest $projectRequest;

    public function mount(): void
    {
        $recordId = Request::query('record');
        if (!$recordId) {
            abort(404);
        }
        $this->projectRequest = ProjectRequest::findOrFail($recordId);
    }

    public function getTitle(): string
    {
        return 'Rekap Absensi: ' . $this->projectRequest->name;
    }

    /**
     * Metode ini mengambil data untuk modal berdasarkan argumen yang dikirim dari Blade.
     */
    public function getMountedActionData(string $employeeId, string $date): array
    {
        $attendance = $this->projectRequest
            ->projectAttendances() // Gunakan relasi untuk query
            ->where('employee_id', $employeeId)
            ->where('tanggal', $date)
            ->first();

        if (!$attendance) {
            return []; // Kembalikan array kosong jika tidak ditemukan
        }

        // Siapkan data untuk ditampilkan di modal
        return [
            'name' => $attendance->employee->user->name,
            'tanggal_absen' => Carbon::parse($attendance->tanggal)->isoFormat('dddd, D MMMM YYYY'),
            'jam_absen' => Carbon::parse($attendance->created_at)->format('H:i:s'),
            'lokasi_maps' => $attendance->lokasi_maps,
            'foto' => $attendance->foto,
        ];
    }

    /**
     * Metode ini mendefinisikan Aksi "detail" yang akan menampilkan modal.
     */
    public function detailAction(): Action
    {
        return Action::make('detail')
            ->modalContent(fn(array $arguments): Htmlable => view(
                'filament.infolists.attendance-detail',
                ['data' => $this->getMountedActionData($arguments['employeeId'], $arguments['date'])]
            ))
            ->modalHeading('Detail Absensi')
            ->modalSubmitAction(false) // Sembunyikan tombol "Submit"
            ->modalCancelActionLabel('Tutup');
    }
}
