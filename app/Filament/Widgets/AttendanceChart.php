<?php

namespace App\Filament\Widgets;

use App\Models\ProjectAttendance;
use App\Models\ProjectRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Tingkat Kepatuhan Absensi Harian';
    protected static ?string $description = 'Grafik ini menampilkan persentase karyawan yang hadir dibandingkan dengan total karyawan yang ditugaskan setiap harinya.';
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = '7d';

    protected function getFilters(): ?array
    {
        return [
            '7d' => '7 Hari Terakhir',
            '14d' => '14 Hari Terakhir',
            '30d' => '30 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            '14d' => 14,
            '30d' => 30,
            default => 7,
        };
        $period = Carbon::today()->subDays($days - 1)->toPeriod(Carbon::today());
        $labels = [];
        $attendanceRateData = [];

        foreach ($period as $date) {
            $labels[] = $date->format('d M');

            $presentCount = ProjectAttendance::whereDate('tanggal', $date)
                ->distinct('employee_id')
                ->count();

            // --- INI BAGIAN YANG DIPERBAIKI SESUAI STRUKTUR ANDA ---
            $employeeIdArrays = ProjectRequest::where('status', 'approved')
                ->where('start_period', '<=', $date)
                ->where('end_period', '>=', $date)
                ->pluck('sdm_ids');

            $targetCount = $employeeIdArrays
                ->flatten()
                ->filter()
                ->unique()
                ->count();

            if ($targetCount > 0) {
                $rate = round(($presentCount / $targetCount) * 100, 2);
            } else {
                $rate = 100;
            }
            $attendanceRateData[] = $rate;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Kehadiran (%)',
                    'data' => $attendanceRateData,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
