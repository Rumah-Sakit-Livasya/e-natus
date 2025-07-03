<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ListProjectForAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'Absensi Proyek';
    protected static string $view = 'filament.pages.list-project-for-attendance';
    protected static ?string $title = 'Pilih Proyek untuk Mengelola Absensi';

    /**
     * Method ini mendefinisikan tabel untuk halaman daftar proyek.
     * Ini TIDAK memerlukan getProjectRequest() karena ia mengambil semua data.
     */
    public function table(Table $table): Table
    {
        return $table
            // Query langsung ke model ProjectRequest untuk mendapatkan semua data.
            ->query(ProjectRequest::query())
            ->columns([
                TextColumn::make('name')->label('Nama Proyek')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Klien')->sortable(),
                TextColumn::make('start_period')->label('Periode Mulai')->date(),
                TextColumn::make('end_period')->label('Periode Selesai')->date(),
                TextColumn::make('status')->badge()->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'done' => 'success',
                })->sortable(),
            ])
            ->actions([
                Action::make('manage_attendance')
                    ->label('Kelola Absensi')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    // Membuat URL yang benar dengan query string
                    ->url(function (ProjectRequest $record): string {
                        $baseUrl = ManageProjectAttendance::getUrl();
                        return "{$baseUrl}?record={$record->id}";
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}
