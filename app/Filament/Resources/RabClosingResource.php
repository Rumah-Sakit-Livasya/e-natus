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
use Filament\Tables\Columns\ImageColumn;

class RabClosingResource extends Resource
{
    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = \App\Filament\Clusters\ProjectCluster::class;

    protected static ?string $model = RabClosing::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'RAB Closing';
    protected static ?string $pluralLabel = 'RAB Closings';

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
                        // ->relationship()
                        ->label('Item Operasional')
                        ->schema([
                            TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                            TextInput::make('price')->label('Harga')->numeric()->prefix('Rp')->required()->columnSpan(1),

                            FileUpload::make('attachments')
                                ->label('Bukti/Struk')
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->disk('public')
                                ->directory('rab-attachments/operasional')
                                ->storeFileNamesIn('original_filename')
                                ->columnSpan(2),
                        ])
                        ->deleteAction(
                            fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                        )
                        ->columns(5)
                        ->reorderable(false)->addActionLabel('Tambah Item Operasional')
                        ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                ]),

            Section::make('Fee Petugas MCU')
                ->schema([
                    Repeater::make('feePetugasItems')
                        ->relationship('feePetugasItems') // Aktifkan relasi agar data muncul
                        ->label('Item Fee Petugas')
                        ->schema([
                            TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                            TextInput::make('price')->label('Harga')->numeric()->prefix('Rp')->required()->columnSpan(1),

                            FileUpload::make('attachments')
                                ->label('Bukti/Struk')
                                ->multiple()
                                ->reorderable()
                                ->appendFiles()
                                ->disk('public')
                                ->directory('rab-attachments/fee')
                                ->storeFileNamesIn('original_filename')
                                ->columnSpan(2),
                        ])
                        ->deleteAction(
                            fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                        )
                        ->columns(5)
                        ->reorderable(false)->addActionLabel('Tambah Item Fee')
                        ->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateAllTotals($get, $set)),
                ]),

            Section::make('BMHP (Bahan Medis Habis Pakai)')
                ->schema([
                    Repeater::make('bmhpItems')
                        ->relationship('bmhpItems') // Aktifkan relasi agar data BMHP muncul
                        ->label('Item BMHP')
                        ->schema([
                            Section::make('Data Planning (Referensi)')
                                ->description('Rencana anggaran dan quantity awal.')
                                ->icon('heroicon-o-document-text')
                                ->compact()
                                ->schema([
                                    Forms\Components\Grid::make(5)->schema([
                                        TextInput::make('name')->label('Item/Deskripsi')->readOnly(),
                                        TextInput::make('satuan')->label('Satuan Master')->readOnly(),
                                        TextInput::make('jumlah_rencana')->label('Rencana (Pcs)')->numeric()->readOnly(),
                                        TextInput::make('pcs_per_unit_snapshot')->label('Isi (Pcs)')->numeric()->readOnly(),
                                        TextInput::make('harga_satuan')->label('Harga Satuan')->numeric()->prefix('Rp')->readOnly(),
                                    ]),
                                ])->columnSpanFull(),

                            Section::make('Aktual Pengembalian / Sisa')
                                ->description('Jumlah barang yang kembali (tidak habis terpakai).')
                                ->icon('heroicon-o-arrow-uturn-left')
                                ->compact()
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Select::make('sisa_purchase_type')
                                            ->label('Sisa Per')
                                            ->options(function (Get $get) {
                                                $satuan = $get('satuan') ?: 'Unit';
                                                return [
                                                    'unit' => "$satuan (unit)",
                                                    'pcs' => 'Pcs',
                                                ];
                                            })
                                            ->default('pcs')
                                            ->required()
                                            ->live()
                                            ->native(false)
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateJumlahSisa($get, $set)),
                                        TextInput::make('sisa_qty')
                                            ->label('Jumlah Sisa')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateJumlahSisa($get, $set)),

                                        TextInput::make('jumlah_sisa')
                                            ->label('Total Pcs Sisa')
                                            ->numeric()
                                            ->default(0)
                                            ->readOnly()
                                            ->helperText('Otomatis: Sisa Qty x Isi (Cek Master).'),
                                    ]),
                                ])->columnSpanFull(),

                            Section::make('Hasil Akhir & Dokumentasi')
                                ->compact()
                                ->schema([
                                    Forms\Components\Grid::make(4)->schema([
                                        TextInput::make('total')
                                            ->label('Total Biaya Terpakai')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(fn(Get $get): float => ((float)($get('jumlah_rencana') ?? 0)) * ((float)($get('harga_satuan') ?? 0)))
                                            ->columnSpan(1)
                                            ->extraInputAttributes(['class' => 'font-bold text-primary-600 dark:text-primary-400']),

                                        FileUpload::make('attachments')
                                            ->label('Struk / Bukti Nota')
                                            ->multiple()
                                            ->reorderable()
                                            ->appendFiles()
                                            ->disk('public')
                                            ->directory('rab-attachments/bmhp')
                                            ->storeFileNamesIn('original_filename')
                                            ->columnSpan(3),
                                    ]),
                                ])->columnSpanFull(),

                            Forms\Components\Hidden::make('pcs_per_unit_snapshot')->default(1),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false),
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
                            ->columnSpanFull(),

                        Section::make('Dokumentasi dan Catatan Tambahan')
                            ->collapsible()
                            ->schema([
                                Textarea::make('keterangan')
                                    ->label('Catatan / Keterangan Closing')
                                    ->placeholder('Tambahkan catatan penting atau ringkasan terkait proses closing proyek ini.')
                                    ->columnSpanFull(),

                                FileUpload::make('documentation')
                                    ->label('Unggah Dokumentasi Proyek (Foto, Laporan, dll.)')
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->disk('public')
                                    ->directory('rab-closing-documentation')
                                    ->storeFileNamesIn('original_filename')
                                    ->columnSpanFull(),
                            ])
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
        // 1. Hitung total dari ketiga repeater: operasional, fee, dan bmhp
        $operasionalItems = $get('operasionalItems') ?? [];
        $feePetugasItems = $get('feePetugasItems') ?? [];
        $bmhpItems = $get('bmhpItems') ?? [];

        $totalOperasional = array_reduce($operasionalItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price']), 0);
        $totalFee = array_reduce($feePetugasItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price']), 0);
        $totalBmhp = array_reduce($bmhpItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['total']), 0);

        $totalBiayaClosing = $totalOperasional + $totalFee + $totalBmhp;

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

    public static function updateJumlahSisa(Get $get, Set $set): void
    {
        $sisaQty = (float) ($get('sisa_qty') ?? 0);
        $purchaseType = (string) ($get('sisa_purchase_type') ?? 'pcs');
        $pcsPerUnit = (int) ($get('pcs_per_unit_snapshot') ?? 1);

        // Calculate total pieces for remaining (jumlah_sisa)
        $totalPcsSisa = 0;
        if ($purchaseType === 'pcs') {
            $totalPcsSisa = $sisaQty;
        } else {
            $multiplier = $pcsPerUnit > 0 ? $pcsPerUnit : 1;
            $totalPcsSisa = $sisaQty * $multiplier;
        }
        $set('jumlah_sisa', $totalPcsSisa);
    }

    public static function updateBmhpRowCalculations(Get $get, Set $set): void
    {
        // This function is kept for compatibility but no longer performs automatic calculations
        // The total field is now manually editable and should match the initial planning cost
        self::updateJumlahSisa($get, $set);

        // Update Global Totals
        $operasionalItems = $get('../../operasionalItems') ?? [];
        $feePetugasItems = $get('../../feePetugasItems') ?? [];
        $bmhpItems = $get('../../bmhpItems') ?? [];

        $totalOperasional = array_reduce($operasionalItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price'] ?? 0), 0);
        $totalFee = array_reduce($feePetugasItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['price'] ?? 0), 0);
        $totalBmhp = array_reduce($bmhpItems, fn($carry, $item) => $carry + self::cleanMoneyValue($item['total'] ?? 0), 0);

        $totalBiayaClosing = $totalOperasional + $totalFee + $totalBmhp;
        $set('../../total_anggaran_closing', $totalBiayaClosing);
        $set('../../pengeluaran_operasional_closing', $totalOperasional);

        $nilaiInvoice = self::cleanMoneyValue($get('../../nilai_invoice_closing'));
        $set('../../margin_closing', $nilaiInvoice - $totalBiayaClosing);

        $danaTransfer = self::cleanMoneyValue($get('../../dana_operasional_transfer'));
        $set('../../sisa_dana_operasional', $danaTransfer - $totalOperasional);
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
                ImageColumn::make('documentation')->label('Dokumentasi')->circular()->stacked()->limit(3)->defaultImageUrl(url('/placeholder.png')),
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

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view rab closing');
    }
}
