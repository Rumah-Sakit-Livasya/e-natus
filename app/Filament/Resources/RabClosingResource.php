<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RabClosingResource\Pages;
use App\Models\RabClosing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Pages\CompareRab;

class RabClosingResource extends Resource
{
    protected static ?string $model = RabClosing::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Project';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Grid layout tidak berubah
                // Gunakan Grid untuk mengatur layout multi-baris
                Forms\Components\Grid::make()
                    ->columns(12) // Tentukan jumlah total kolom dasar untuk grid
                    ->schema([
                        // --- BARIS PERTAMA --- (Tidak ada perubahan)
                        Placeholder::make('project_name')
                            ->label('Proyek')
                            ->content(fn(?RabClosing $record): string => $record?->projectRequest->name ?? '-')
                            ->columnSpan(4),

                        DatePicker::make('closing_date')
                            ->required()->label('Tanggal Closing')
                            ->columnSpan(4),

                        Select::make('status')
                            ->options(['draft' => 'Draft', 'final' => 'Final'])
                            ->disabled()->required()
                            ->columnSpan(4),

                        // --- BARIS KEDUA --- (PERBAIKAN DI SINI)
                        TextInput::make('total_anggaran')
                            ->label('Anggaran Awal Proyek')
                            ->prefix('Rp')
                            ->readOnly()
                            ->formatStateUsing(fn(?string $state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => preg_replace('/[^\d]/', '', $state))
                            ->columnSpan(3),

                        TextInput::make('total_realisasi')
                            ->label('Total Realisasi')
                            ->readOnly()
                            ->prefix('Rp')
                            ->formatStateUsing(fn(?string $state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => preg_replace('/[^\d]/', '', $state))
                            ->columnSpan(3),

                        TextInput::make('selisih')
                            ->label('Selisih')
                            ->readOnly()
                            ->prefix('Rp')
                            ->formatStateUsing(fn(?string $state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => preg_replace('/[^\d]/', '', $state))
                            ->columnSpan(3),

                        TextInput::make('total_anggaran_closing')
                            ->label('Total Anggaran Closing')
                            ->readOnly()
                            ->prefix('Rp')
                            ->formatStateUsing(fn(?string $state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => preg_replace('/[^\d]/', '', $state))
                            ->columnSpan(3),
                    ]),

                // Repeater yang sudah disederhanakan
                Repeater::make('items')
                    ->label('Item Anggaran Closing')
                    ->relationship()
                    ->columnSpanFull()
                    // Hanya butuh satu pemicu utama di sini
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?array $state) {
                        // Panggil fungsi kalkulasi utama
                        self::updateAllTotals($get, $set, $state);
                    })
                    ->schema([
                        TextInput::make('description')->required()->columnSpan(2),
                        TextInput::make('qty')
                            ->numeric()->required()
                            // Perubahan di sini akan ditangkap oleh Repeater
                            ->live(onBlur: true),
                        TextInput::make('harga_satuan')
                            ->label('Harga Anggaran Closing')->prefix('Rp')->required()
                            ->mask(RawJs::make('$money($input)'))->stripCharacters(',')
                            // Perubahan di sini juga akan ditangkap oleh Repeater
                            ->live(onBlur: true),
                        // Field ini akan di-update oleh kalkulasi
                        TextInput::make('total_anggaran')
                            ->label('Total Anggaran Item')->prefix('Rp')->readOnly()
                            ->formatStateUsing(fn(?string $state) => number_format(self::cleanMoneyValue($state), 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => self::cleanMoneyValue($state)),
                    ])
                    ->columns(5)
                    ->reorderableWithButtons()
                    ->addActionLabel('Tambah Item')
                    ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
                    ->disabled(fn(?RabClosing $record) => $record?->status === 'final'),
            ]);
    }

    private static function cleanMoneyValue(?string $value): float
    {
        return (float) preg_replace('/[^\d]/', '', $value ?? '0');
    }

    /**
     * Fungsi tunggal untuk menghitung dan memperbarui SEMUA nilai.
     */
    private static function updateAllTotals(Get $get, Set $set, ?array $state): void
    {
        if ($state === null) {
            return;
        }

        $items = $state; // Ambil state repeater saat ini
        $grandTotalAnggaranClosing = 0;

        // 1. Loop untuk MENGHITUNG ULANG total per item dan MENJUMLAHKAN grand total
        foreach ($items as $key => $item) {
            $qty = (int) ($item['qty'] ?? 0);
            $hargaSatuan = self::cleanMoneyValue($item['harga_satuan']);
            $totalItem = $qty * $hargaSatuan;

            // Simpan total per item yang baru dihitung ke dalam array
            $items[$key]['total_anggaran'] = $totalItem;

            // Tambahkan ke grand total
            $grandTotalAnggaranClosing += $totalItem;
        }

        // Ambil nilai realisasi dari form state (yang dimuat dari DB)
        $totalRealisasi = self::cleanMoneyValue($get('total_realisasi'));

        // Hitung selisihnya
        $selisih = $grandTotalAnggaranClosing - $totalRealisasi;

        // 2. SET ULANG semua nilai yang relevan dalam satu operasi
        $set('items', $items); // Update repeater dengan total per item yang benar
        $set('total_anggaran_closing', $grandTotalAnggaranClosing); // Update grand total
        $set('selisih', $selisih); // Update selisih
    }

    /**
     * Membersihkan data form sebelum disimpan untuk mencegah error.
     */
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return self::cleanAllMoneyFields($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return self::cleanAllMoneyFields($data);
    }

    protected static function cleanAllMoneyFields(array $data): array
    {
        // Tetap bersihkan semua field uang sebagai pengaman
        $moneyFields = ['total_anggaran_closing', 'total_realisasi', 'selisih'];
        foreach ($moneyFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = self::cleanMoneyValue($data[$field]);
            }
        }
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('projectRequest.name')
                    ->searchable()->sortable()->label('Nama Proyek'),
                TextColumn::make('closing_date')
                    ->date('d M Y')->sortable()->label('Tanggal Closing'),
                TextColumn::make('total_anggaran_closing') // Tampilkan anggaran closing
                    ->label('Anggaran Closing')->numeric()->money('IDR')->sortable(),
                TextColumn::make('total_realisasi')
                    ->label('Total Realisasi')->numeric()->money('IDR')->sortable(),
                TextColumn::make('selisih')
                    ->label('Selisih')->numeric()->money('IDR')->sortable()
                    ->color(fn(int $state): string => $state >= 0 ? 'success' : 'danger'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('compare')
                    ->label('Bandingkan')->icon('heroicon-o-scale')
                    ->url(fn(RabClosing $record): string => CompareRab::getUrl(['record' => $record->project_request_id]))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRabClosings::route('/'),
            // 'create' => Pages\CreateRabClosing::route('/create'), // Dihilangkan karena dibuat dari Project Request
            'edit' => Pages\EditRabClosing::route('/{record}/edit'),
        ];
    }
}
