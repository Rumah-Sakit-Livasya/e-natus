<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProjectRequestResource;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class PaymentReminderWidget extends BaseWidget
{
    protected static ?string $heading = 'Pengingat Pembayaran Proyek';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return ProjectRequest::query()
                    ->whereIn('status_pembayaran', ['unpaid', 'partial paid'])
                    ->where('due_date', '<=', Carbon::today()->addDays(7));
            })
            ->defaultSort('due_date', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('client.name')
                    ->label('Klien')
                    ->searchable(),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    // PERBAIKAN: Menggunakan isPast() lebih aman dan
                    // mengecek apakah due_date tidak null
                    ->color(fn(ProjectRequest $record) => $record->due_date?->isPast() ? 'danger' : 'warning')
                    ->tooltip(function (ProjectRequest $record): string {
                        // PERBAIKAN: Menangani jika due_date kosong
                        if (!$record->due_date) {
                            return 'Tanggal jatuh tempo belum diatur';
                        }
                        // Menggunakan metode Carbon yang lebih modern dan aman
                        if ($record->due_date->isPast()) {
                            return 'Terlambat ' . $record->due_date->diffInDays(Carbon::today()) . ' hari';
                        }
                        return 'Jatuh tempo dalam ' . Carbon::today()->diffInDays($record->due_date) . ' hari';
                    }),

                TextColumn::make('nilai_invoice')
                    ->label('Nilai Invoice')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partial paid' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Lihat')
                    ->url(fn(ProjectRequest $record): string => ProjectRequestResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
