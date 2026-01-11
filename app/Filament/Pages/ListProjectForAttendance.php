<?php

namespace App\Filament\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\ProjectRequest;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action as TableAction;
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

    public static function shouldRegisterNavigation(): bool
    {
        // Hanya tampilkan menu jika user boleh melihat halaman ini
        return static::canViewAny();
    }

    public function table(Table $table): Table
    {
        return $table
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
                TableAction::make('manage_attendance')
                    ->label('Kelola Absensi')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->hidden(fn(ProjectRequest $record): bool => $record->status === 'pending')
                    ->url(function (ProjectRequest $record): string {
                        $baseUrl = ManageProjectAttendance::getUrl();
                        return "{$baseUrl}?record={$record->id}";
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('report')
                ->label('Laporan Absensi')
                ->icon('heroicon-o-chart-bar-square')
                ->color('info')
                ->url(EmployeeResource::getUrl('report')),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()?->user();
        if (!$user) {
            return false;
        }
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }
        return $user->can('view attendance project');
    }
}
