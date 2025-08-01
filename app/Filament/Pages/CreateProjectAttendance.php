<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateProjectAttendance extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.create-project-attendance';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $routePath = '/dashboard/create-project-attendance';

    public ?array $data = [];
    public ProjectRequest $projectRequest;

    public function mount(): void
    {
        $recordId = Request::query('projectRequest');
        if (!$recordId) {
            abort(404);
        }
        $this->projectRequest = ProjectRequest::findOrFail($recordId);
    }

    public function getTitle(): string
    {
        return 'Form Absensi: ' . $this->projectRequest->name;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('employee_name')
                    ->label('Nama Karyawan')
                    ->content(auth()->user()->name ?? 'N/A'),
                Placeholder::make('date')
                    ->label('Tanggal')
                    ->content(now()->isoFormat('D MMMM YYYY')),
                ViewField::make('location_and_photo')
                    ->label('Lokasi & Foto Bukti') // Label utama untuk komponen gabungan
                    ->view('forms.components.location-picker-with-camera'),

                // TAMBAHKAN KOMPONEN TEXTAREA DI SINI
                Textarea::make('notes')
                    ->label('Catatan (Opsional)')
                    ->placeholder('Contoh: Sinyal GPS lemah, posisi kurang akurat.')
                    ->rows(3),

                Hidden::make('lokasi_maps')->required(),
                Hidden::make('foto')->required(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Kirim Absen')
                ->extraAttributes(['class' => 'mt-3'])
                ->submit('create'),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();
        if (empty($data['lokasi_maps']) || empty($data['foto'])) {
            Notification::make()->title('Data Tidak Lengkap')->body('Pastikan lokasi sudah terdeteksi dan foto sudah diambil.')->danger()->send();
            return;
        }
        $employeeId = auth()->user()->employee->id;
        $today = now()->toDateString();
        try {
            DB::beginTransaction();
            $exists = $this->projectRequest->projectAttendances()->where('employee_id', $employeeId)->whereDate('tanggal', $today)->exists();
            if ($exists) {
                Notification::make()->title('Gagal')->body('Anda sudah absen untuk proyek ini hari ini.')->danger()->send();
                DB::rollBack();
                return;
            }
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['foto']));
            $imageName = 'absensi/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($imageName, $imageData);
            $this->projectRequest->projectAttendances()->create([
                'employee_id' => $employeeId,
                'tanggal' => $today,
                'lokasi_maps' => $data['lokasi_maps'],
                'foto' => $imageName,
                'notes' => $data['notes'],
            ]);
            DB::commit();
            Notification::make()->title('Berhasil')->body('Absensi berhasil direkam.')->success()->send();
            $this->redirect(ManageProjectAttendance::getUrl(['record' => $this->projectRequest->id]));
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Terjadi Kesalahan')->body($e->getMessage())->danger()->send();
        }
    }
}
