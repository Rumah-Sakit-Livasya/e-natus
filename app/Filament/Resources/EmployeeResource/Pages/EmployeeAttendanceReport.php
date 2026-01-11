<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\ProjectAttendance;
use App\Models\ProjectRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeAttendanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    // == BAGIAN PROPERTI STATIC (Pastikan tidak ada duplikat) ==
    protected static string $resource = EmployeeResource::class;
    protected static string $view = 'filament.resources.employee-resource.pages.employee-attendance-report';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $title = 'Laporan Absensi Karyawan';
    protected static ?string $navigationLabel = 'Laporan Absensi';

    // == BAGIAN PROPERTI LIVEWIRE ==
    public ?int $employee_id = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public Collection $report_data;
    public array $summary_stats = [];

    // == BAGIAN METHOD ==
    public function mount(): void
    {
        $this->start_date = now()->subMonth()->startOfDay()->toDateString();
        $this->end_date = now()->endOfDay()->toDateString();
        $this->report_data = collect();
    }

    // Di dalam file EmployeeAttendanceReport.php

    protected function getFormSchema(): array
    {
        return [
            Select::make('employee_id')
                ->label('Pilih Karyawan')
                ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                ->searchable()
                ->required(),
            DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->required(),
            DatePicker::make('end_date')
                ->label('Tanggal Selesai')
                ->required(),
        ];
    }


    protected function getFormActions(): array
    {
        return [
            Action::make('generateReport')
                ->label('Buat Laporan')
                ->submit('generateReport')
                ->extraAttributes(['class' => 'mt-3']),
        ];
    }

    // Di dalam file EmployeeAttendanceReport.php

    public function generateReport(): void
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = Employee::with('user')->find($this->employee_id);

        $attendancesByDate = ProjectAttendance::where('employee_id', $this->employee_id)
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->get()
            ->keyBy(function ($item) {
                return $item->tanggal . '-' . $item->project_request_id;
            });

        $period = Carbon::parse($this->start_date)->toPeriod(Carbon::parse($this->end_date));
        $this->report_data = collect();
        $totalAssignedDays = 0;

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            // 1. Cari SEMUA proyek di mana karyawan ditugaskan pada hari ini
            $assignedProjects = ProjectRequest::where('status', 'approved')
                // ==========================================================
                // ===== INI ADALAH PERBAIKANNYA ==========================
                // ===== Ubah pencarian menjadi string agar lebih andal =====
                ->whereJsonContains('sdm_ids', (string)$employee->id)
                // ==========================================================
                ->where('start_period', '<=', $date)
                ->where('end_period', '>=', $date)
                ->get();

            if ($assignedProjects->isEmpty()) {
                $this->report_data->push([
                    'date' => $date->isoFormat('dddd, D MMMM YYYY'),
                    'status' => 'Tidak Ada Penugasan',
                    'project_name' => '-',
                ]);
            } else {
                foreach ($assignedProjects as $project) {
                    $totalAssignedDays++;
                    $attendanceKey = $dateString . '-' . $project->id;

                    if ($attendancesByDate->has($attendanceKey)) {
                        $this->report_data->push([
                            'date' => $date->isoFormat('dddd, D MMMM YYYY'),
                            'status' => 'Hadir',
                            'project_name' => $project->name,
                        ]);
                    } else {
                        $this->report_data->push([
                            'date' => $date->isoFormat('dddd, D MMMM YYYY'),
                            'status' => 'Tidak Hadir',
                            'project_name' => $project->name,
                        ]);
                    }
                }
            }
        }

        $presentDays = $attendancesByDate->count();
        $absentDays = $totalAssignedDays - $presentDays;
        $attendanceRate = $totalAssignedDays > 0 ? round(($presentDays / $totalAssignedDays) * 100, 2) : 0;

        $this->summary_stats = [
            'employee_name' => $employee->user->name,
            'period' => Carbon::parse($this->start_date)->isoFormat('D MMM Y') . ' - ' . Carbon::parse($this->end_date)->isoFormat('D MMM Y'),
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'total_working_days' => $totalAssignedDays,
            'attendance_rate' => $attendanceRate,
        ];
    }
}
