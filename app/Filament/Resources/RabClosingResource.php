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
                            TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                            TextInput::make('price')->label('Harga')->numeric()->prefix('Rp')->required()->columnSpan(1),

                            // --- PERBAIKAN: GUNAKAN NAMA YANG KONSISTEN ---
                            FileUpload::make('attachments_upload') // <-- UBAH MENJADI 'attachments_upload'
                                ->label('Bukti/Struk')
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->disk('public')
                                ->directory('rab-attachments/operasional')
                                ->columnSpan(2), // <-- PERBAIKAN: columnSpan harus 2 agar pas
                        ])
                        ->columns(5)
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

                            FileUpload::make('attachments_upload') // <-- Nama ini sudah benar
                                ->label('Bukti/Struk')
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->disk('public')
                                ->directory('rab-attachments/fee')
                                ->columnSpan(2), // <-- PERBAIKAN: columnSpan harus 2
                        ])
                        ->columns(5)
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
                            // Field ini sudah benar
                            TextInput::make('jumlah_peserta_awal')->numeric()->label('Estimasi Peserta Awal'),
                            TextInput::make('jumlah_peserta_akhir')->numeric()->label('Peserta Setelah Closed'),
                        ]),

                        // --- PERBAIKAN UTAMA ADA DI SECTION INI ---
                        Section::make('RAB Awal')->schema([
                            // Ambil total dari kolom yang sudah ada di $record
                            Placeholder::make('total_rab_awal_placeholder')->label('Total RAB Awal')
                                ->content(fn(?RabClosing $record): string => $record ? 'Rp ' . number_format($record->total_anggaran, 0, ',', '.') : 'Rp 0'),

                            // Ambil nilai invoice dari relasi projectRequest (ini sudah benar)
                            Placeholder::make('nilai_invoice_awal_placeholder')->label('Nilai Invoice Awal')
                                ->content(fn(?RabClosing $record): string => $record ? 'Rp ' . number_format($record->projectRequest->nilai_invoice, 0, ',', '.') : 'Rp 0'),

                            // Add margin calculation placeholder
                            Placeholder::make('margin_awal_placeholder')->label('Margin Awal')
                                ->content(
                                    fn(?RabClosing $record): string => $record
                                        ? 'Rp ' . number_format($record->projectRequest->nilai_invoice - $record->total_anggaran, 0, ',', '.')
                                        : 'Rp 0'
                                ),
                        ]),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Section::make('Dana Operasional')->schema([
                            TextInput::make('dana_operasional_transfer')->numeric()->prefix('Rp')->label('Dana di Transfer oleh Natus')
                                ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                            TextInput::make('pengeluaran_operasional_closing')->numeric()->prefix('Rp')->label('Pengeluaran Operasional Closed')->readOnly(),
                            TextInput::make('sisa_dana_operasional')->numeric()->prefix('Rp')->label('Sisa/Minus Dana')->readOnly(),
                        ]),

                        Forms\Components\RichEditor::make('justifikasi')
                            ->label('Justifikasi Perbedaan RAB')
                            ->required()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo'
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('justifikasi-attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull() // Make the rich editor span full width
                    ]),
                ]),

        ])
            ->disabled(fn(?RabClosing $record) => $record?->status === 'final');
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
