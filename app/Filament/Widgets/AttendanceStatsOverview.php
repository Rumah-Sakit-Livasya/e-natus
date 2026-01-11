<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceSubmission;
use App\Models\ProjectAttendance;
use App\Models\ProjectRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Menghitung karyawan yang hadir hari ini (ini sudah benar)
        $presentToday = ProjectAttendance::whereDate('tanggal', today())->distinct('employee_id')->count();

        // --- 2. INI BAGIAN YANG DIPERBAIKI SESUAI STRUKTUR ANDA ---
        // Ambil koleksi array 'sdm_ids' dari semua proyek yang aktif hari ini.
        $employeeIdArrays = ProjectRequest::where('status', 'approved')
            ->where('start_period', '<=', today())
            ->where('end_period', '>=', today())
            ->pluck('sdm_ids');

        // Gabungkan semua array menjadi satu, hapus duplikat, dan hitung.
        $totalEmployeesForToday = $employeeIdArrays
            ->flatten()       // Mengubah [[1, 2], [2, 3]] menjadi [1, 2, 2, 3]
            ->filter()        // Menghapus nilai null/kosong
            ->unique()        // Menjadi [1, 2, 3]
            ->count();         // Hasil: 3

        $absentToday = $totalEmployeesForToday - $presentToday;

        // 3. Menghitung pengajuan yang pending (ini sudah benar)
        $pendingSubmissions = AttendanceSubmission::where('status', 'pending')->count();

        return [
            Stat::make('Hadir Hari Ini', $presentToday)
                ->description('Total karyawan yang sudah absen')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Tidak Hadir Hari Ini', $absentToday)
                ->description('Dari ' . $totalEmployeesForToday . ' karyawan yang ditugaskan')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),

            Stat::make('Pengajuan Absensi Pending', $pendingSubmissions)
                ->description('Perlu direview dan disetujui')
                ->color('warning')
                ->icon('heroicon-o-document-arrow-up'),
        ];
    }
}
