<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

// Impor semua model pemeriksaan
use App\Models\EkgCheck;
use App\Models\LabCheck;
use App\Models\RontgenCheck;
use App\Models\AudiometryCheck;
use App\Models\DrugTest;
use App\Models\SpirometryCheck;
use App\Models\TreadmillCheck;
use App\Models\UsgAbdomenCheck;
use App\Models\UsgMammaeCheck;

class HasilPemeriksaanWidget extends BaseWidget
{
    protected static ?string $heading = 'Hasil Pemeriksaan';
    protected int | string | array $columnSpan = 'full';

    // 1. Properti untuk menyimpan state filter. Akan dihubungkan ke dropdown.
    public array $filterData = [
        'pemeriksaanType' => 'ekg', // Nilai default
    ];

    public function table(Table $table): Table
    {
        // 2. Ambil nilai filter saat ini DARI PROPERTI PUBLIK.
        $pemeriksaanType = $this->filterData['pemeriksaanType'];

        // 3. Tentukan kolom dinamis berdasarkan nilai filter yang sudah pasti benar.
        $dynamicColumns = match ($pemeriksaanType) {
            'ekg' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('kesimpulan')->label('Kesan'),
            ],
            'lab' => [
                Tables\Columns\TextColumn::make('no_lab')->label('No. Lab')->searchable(),
                Tables\Columns\TextColumn::make('hemoglobin')->label('Hb'),
                Tables\Columns\TextColumn::make('leukosit')->label('Leukosit'),
                Tables\Columns\TextColumn::make('sgot')->label('SGOT'),
                Tables\Columns\TextColumn::make('sgpt')->label('SGPT'),
            ],
            'rontgen' => [
                Tables\Columns\TextColumn::make('no_rontgen')->label('No. Rontgen')->searchable(),
                Tables\Columns\TextColumn::make('kesan')->label('Kesan'),
            ],
            'audiometry' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('derajat_ad')->label('Ambang Dengar Kanan (AD)'),
                Tables\Columns\TextColumn::make('derajat_as')->label('Ambang Dengar Kiri (AS)'),
                Tables\Columns\TextColumn::make('kesimpulan')->label('Kesan'),
            ],
            'drug_test' => [
                Tables\Columns\TextColumn::make('no_mcu')->label('No. MCU')->searchable(),
                Tables\Columns\TextColumn::make('amphetamine')->label('Amphetamine'),
                Tables\Columns\TextColumn::make('thc')->label('THC'),
            ],
            'spirometry' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('kesan')->label('Kesan'),
            ],
            'treadmill' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('kesimpulan')->label('Kesimpulan'),
            ],
            'usg_abdomen' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('kesimpulan')->label('Kesimpulan'),
            ],
            'usg_mammae' => [
                Tables\Columns\TextColumn::make('no_rm')->label('No. RM')->searchable(),
                Tables\Columns\TextColumn::make('kesimpulan')->label('Kesan'),
            ],
            default => [],
        };

        return $table
            ->query(function () use ($pemeriksaanType) {
                // 4. Tentukan query dinamis berdasarkan nilai filter.
                $query = match ($pemeriksaanType) {
                    'ekg' => EkgCheck::query(),
                    'lab' => LabCheck::query(),
                    'rontgen' => RontgenCheck::query(),
                    'audiometry' => AudiometryCheck::query(),
                    'drug_test' => DrugTest::query(),
                    'spirometry' => SpirometryCheck::query(),
                    'treadmill' => TreadmillCheck::query(),
                    'usg_abdomen' => UsgAbdomenCheck::query(),
                    'usg_mammae' => UsgMammaeCheck::query(),
                    default => EkgCheck::query(),
                };
                return $query->with('participant');
            })
            ->columns(array_merge(
                [
                    Tables\Columns\TextColumn::make('participant.name')->label('Nama Peserta')->searchable(),
                    Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
                ],
                $dynamicColumns
            ))
            ->header(
                // Tetap gunakan view untuk menampilkan dropdown
                view('filament.widgets.hasil-pemeriksaan-header')
            )
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Print Laporan Asli')
                    ->icon('heroicon-o-printer')
                    ->url(function ($record) use ($pemeriksaanType): string {
                        $routeMap = [
                            'ekg' => 'ekg.print',
                            'lab' => 'lab.print',
                            'rontgen' => 'rontgen.print',
                            'audiometry' => 'audiometry.print',
                            'drug_test' => 'drug-test.print',
                            'spirometry' => 'spirometri.print',
                            'treadmill' => 'treadmill.print',
                            'usg_abdomen' => 'usg.print',
                            'usg_mammae' => 'usg-mammae.print',
                        ];
                        if (!isset($routeMap[$pemeriksaanType])) return '#';
                        return route($routeMap[$pemeriksaanType], $record);
                    })
                    ->openUrlInNewTab(),
            ]);
    }

    /**
     * ===================================================================
     *                        INI ADALAH KUNCI UTAMANYA
     * ===================================================================
     * Metode ini secara otomatis dipanggil oleh Livewire setiap kali
     * properti $filterData diperbarui oleh dropdown.
     */
    public function updatedFilterData(): void
    {
        // Perintah ini memaksa seluruh tabel untuk menghancurkan state-nya
        // (termasuk query dan kolom lama) dan membangun ulang dari awal.
        // Ini menyelesaikan masalah delay/sinkronisasi.
        $this->resetTable();
    }
}
