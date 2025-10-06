<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceSubmissionResource\Pages;
use App\Models\AttendanceSubmission;
use App\Models\Employee;
use App\Models\ProjectRequest;
use App\Models\ProjectAttendance; // <-- Import model ini
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification; // <-- Import Notifikasi

class AttendanceSubmissionResource extends Resource
{
    protected static ?string $model = AttendanceSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'Pengajuan Absensi Susulan';
    protected static ?string $pluralModelLabel = 'Pengajuan Absensi Susulan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_request_id')
                    ->label('Proyek')
                    ->options(function () {
                        return ProjectRequest::query()
                            ->where('start_period', '<=', now())
                            ->where('end_period', '>=', now())
                            ->where('status', '!=', 'pending')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    // Hapus type-hint 'Set' menjadi '$set'
                    ->afterStateUpdated(function ($set) {
                        $set('tanggal_absensi', null);
                        $set('employee_id', null);
                    }),

                Forms\Components\Select::make('employee_id')
                    ->label('Nama Karyawan')
                    // Hapus type-hint 'Get' menjadi '$get'
                    ->options(function ($get) {
                        $projectId = $get('project_request_id');
                        if (!$projectId) return [];

                        $project = ProjectRequest::find($projectId);
                        return $project?->assignedEmployees()->with('user')->get()->pluck('user.name', 'id') ?? [];
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_absensi')
                    ->label('Tanggal Absensi')
                    ->required()
                    ->minDate(function ($get) {
                        $projectId = $get('project_request_id');
                        if (!$projectId) return null;

                        $project = ProjectRequest::find($projectId);
                        return $project ? \Carbon\Carbon::parse($project->start_period) : null;
                    })
                    ->maxDate(function ($get) {
                        $projectId = $get('project_request_id');
                        if (!$projectId) return null;

                        $project = ProjectRequest::find($projectId);
                        return $project ? \Carbon\Carbon::parse($project->end_period) : null;
                    })
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->timezone('Asia/Jakarta')
                    ->reactive(),

                Forms\Components\Textarea::make('alasan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('foto_bukti')
                    ->image()
                    ->directory('bukti-pengajuan-absensi')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('submitted_by')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectRequest.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_absensi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('submitter.name')->label('Diinput oleh')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewer.name')->label('Direview oleh')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                // Tombol Aksi Kustom untuk Approve/Reject
                self::getApproveAction(),
                self::getRejectAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Fungsi untuk membuat tombol "Setujui"
    protected static function getApproveAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('approve')
            ->label('Setujui')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation() // Meminta konfirmasi sebelum eksekusi
            ->modalHeading('Setujui Absensi')
            ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan absensi ini? Data absensi akan dibuat secara otomatis.')
            ->action(function (AttendanceSubmission $record) {
                // Cek duplikat
                if (ProjectAttendance::where('employee_id', $record->employee_id)->where('tanggal', $record->tanggal_absensi)->exists()) {
                    Notification::make()->title('Gagal')->body('Karyawan ini sudah memiliki data absensi di tanggal tersebut.')->danger()->send();
                    return;
                }

                // Buat data absensi
                ProjectAttendance::create([
                    'project_request_id' => $record->project_request_id,
                    'employee_id' => $record->employee_id,
                    'tanggal' => $record->tanggal_absensi,
                    'lokasi_maps' => 'Absensi diajukan manual',
                    'foto' => $record->foto_bukti,
                    'notes' => 'Disetujui via pengajuan manual. Alasan: ' . $record->alasan,
                ]);

                // Update status pengajuan
                $record->update([
                    'status' => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                ]);

                Notification::make()->title('Berhasil')->body('Pengajuan absensi telah disetujui.')->success()->send();
            })
            // Hanya tampilkan tombol ini jika statusnya "pending"
            ->visible(fn(AttendanceSubmission $record): bool => $record->status === 'pending');
    }

    // Fungsi untuk membuat tombol "Tolak"
    protected static function getRejectAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('reject')
            ->label('Tolak')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->form([
                // Minta alasan penolakan
                Forms\Components\Textarea::make('review_notes')
                    ->label('Alasan Penolakan')
                    ->required(),
            ])
            ->action(function (AttendanceSubmission $record, array $data) {
                $record->update([
                    'status' => 'rejected',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'review_notes' => $data['review_notes'],
                ]);
                Notification::make()->title('Pengajuan Ditolak')->success()->send();
            })
            ->visible(fn(AttendanceSubmission $record): bool => $record->status === 'pending');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSubmissions::route('/'),
            'create' => Pages\CreateAttendanceSubmission::route('/create'),
            'edit' => Pages\EditAttendanceSubmission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view request attendance project');
    }
}
