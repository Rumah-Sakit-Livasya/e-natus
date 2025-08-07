<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RabClosingResource\Pages;
use App\Models\RabClosing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class RabClosingResource extends Resource
{
    protected static ?string $model = RabClosing::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Project';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Umum')
                ->schema([
                    Placeholder::make('project_name')
                        ->label('Proyek')
                        ->content(fn(?RabClosing $record): string => $record?->projectRequest->name ?? '-'),
                    DatePicker::make('closing_date')->required()->label('Tanggal Closing'),
                    Select::make('status')->options(['draft' => 'Draft', 'final' => 'Final'])->disabled()->required(),
                ])->columns(3),

            Section::make('Operasional MCU')
                ->schema([
                    Repeater::make('operasionalItems')
                        ->relationship()
                        ->label('Item Operasional')
                        ->schema([
                            // Kita ubah layout kolomnya
                            TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                            TextInput::make('price')->label('Harga')->numeric()->prefix('Rp')->required()->columnSpan(1),

                            // TAMBAHKAN KOMPONEN FILE UPLOAD DI SINI
                            FileUpload::make('attachment')
                                ->label('Bukti/Struk')
                                ->disk('public') // Menyimpan di storage/app/public
                                ->directory('rab-attachments/operasional') // Membuat folder khusus
                                ->columnSpan(2),

                        ])
                        ->columns(5) // Total kolom tetap 5
                        ->reorderable(false)->addActionLabel('Tambah Item Operasional')
                        ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                ]),

            Section::make('Fee Petugas MCU')
                ->schema([
                    Repeater::make('feePetugasItems')
                        ->relationship()
                        ->label('Item Fee Petugas')
                        ->schema([
                            TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                            TextInput::make('price')->label('Harga')->numeric()->prefix('Rp')->required()->columnSpan(1),

                            // TAMBAHKAN KOMPONEN FILE UPLOAD DI SINI JUGA
                            FileUpload::make('attachment')
                                ->label('Bukti/Struk')
                                ->disk('public')
                                ->directory('rab-attachments/fee') // Folder berbeda untuk kerapian
                                ->columnSpan(2),
                        ])
                        ->columns(5) // Total kolom tetap 5
                        ->reorderable(false)->addActionLabel('Tambah Item Fee')
                        ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                ]),

            // Kalkulasi Total Utama
            Section::make('Kalkulasi Total')
                ->schema([
                    TextInput::make('total_anggaran_closing')->label('Total Biaya (Closing)')->numeric()->prefix('Rp')->readOnly(),
                    TextInput::make('nilai_invoice_closing')->label('Nilai Invoice')->numeric()->prefix('Rp')
                        ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                    TextInput::make('margin_closing')->label('Margin')->numeric()->prefix('Rp')->readOnly(),
                ])->columns(3),

            // Laporan dan Justifikasi (dari halaman 2)
            Section::make('Laporan dan Justifikasi')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Section::make('Data Peserta MCU')->schema([
                            TextInput::make('jumlah_peserta_awal')->numeric()->label('Estimasi Peserta Awal'),
                            TextInput::make('jumlah_peserta_akhir')->numeric()->label('Peserta Setelah Closed'),
                        ]),
                        Section::make('RAB Awal')->schema([
                            Placeholder::make('total_rab_awal_placeholder')->label('Total RAB Awal')
                                ->content(fn(?RabClosing $record) => 'Rp ' . number_format($record->projectRequest->rencanaAnggaranBiaya()->sum('total'), 0, ',', '.')),
                            Placeholder::make('nilai_invoice_awal_placeholder')->label('Nilai Invoice Awal')
                                ->content(fn(?RabClosing $record) => 'Rp ' . number_format($record->projectRequest->nilai_invoice, 0, ',', '.')),
                        ]),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Section::make('Dana Operasional')->schema([
                            TextInput::make('dana_operasional_transfer')->numeric()->prefix('Rp')->label('Dana di Transfer oleh Natus')
                                ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                            TextInput::make('pengeluaran_operasional_closing')->numeric()->prefix('Rp')->label('Pengeluaran Operasional Closed')->readOnly(),
                            TextInput::make('sisa_dana_operasional')->numeric()->prefix('Rp')->label('Sisa/Minus Dana')->readOnly(),
                        ]),
                        Textarea::make('justifikasi')->label('Justifikasi Perbedaan RAB')->rows(8),
                    ]),
                ]),
        ]);
    }

    private static function cleanMoneyValue(?string $value): float
    {
        return (float) preg_replace('/[^\d.]/', '', $value ?? '0');
    }

    /**
     * Fungsi tunggal untuk menghitung dan memperbarui SEMUA nilai.
     */
    private static function updateAllTotals(Get $get, Set $set): void
    {
        // 1. Hitung total dari kedua repeater
        $operasionalItems = $get('operasionalItems') ?? [];
        $feePetugasItems = $get('feePetugasItems') ?? [];

        $totalOperasional = array_reduce($operasionalItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price']), 0);
        $totalFee = array_reduce($feePetugasItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price']), 0);

        $totalBiayaClosing = $totalOperasional + $totalFee;

        // 2. Hitung margin
        $nilaiInvoice = self::cleanMoneyValue($get('nilai_invoice_closing'));
        $margin = $nilaiInvoice - $totalBiayaClosing;

        // 3. Hitung sisa dana operasional
        $danaTransfer = self::cleanMoneyValue($get('dana_operasional_transfer'));
        $sisaDana = $danaTransfer - $totalOperasional; // Sisa dana hanya dihitung dari biaya operasional

        // 4. Set semua nilai yang dihitung
        $set('total_anggaran_closing', $totalBiayaClosing);
        $set('margin_closing', $margin);
        $set('pengeluaran_operasional_closing', $totalOperasional);
        $set('sisa_dana_operasional', $sisaDana);
    }

    public static function table(Table $table): Table
    {
        // Fungsi table() Anda sudah cukup baik dan tidak perlu diubah.
        return $table
            ->columns([
                TextColumn::make('projectRequest.name')->searchable()->sortable()->label('Nama Proyek'),
                TextColumn::make('closing_date')->date('d M Y')->sortable()->label('Tanggal Closing'),
                TextColumn::make('total_anggaran_closing')->label('Total Biaya Closing')->numeric()->money('IDR')->sortable(),
                TextColumn::make('margin_closing')->label('Margin')->numeric()->money('IDR')->sortable(),
                TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    // Fungsi lainnya (getRelations, getPages) bisa tetap sama.
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRabClosings::route('/'),
            'edit' => Pages\EditRabClosing::route('/{record}/edit'),
            // 'view' => Pages\ViewRabClosing::route('/{record}'),
        ];
    }
}
