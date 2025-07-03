<?php

namespace App\Filament\Resources;

use App\Enums\StatusPengajuanEnum;
use App\Filament\Resources\PengajuanDanaResource\Pages;
use App\Models\PengajuanDana;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class PengajuanDanaResource extends Resource
{
    protected static ?string $model = PengajuanDana::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $pluralModelLabel = 'Pengajuan Dana';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Detail Pengajuan')
                        ->schema([
                            Forms\Components\Select::make('project_request_id')
                                ->relationship('projectRequest', 'name')
                                ->label('Proyek')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Forms\Components\TextInput::make('tujuan')
                                ->required()
                                ->maxLength(255),

                            // ========================================================
                            // == BAGIAN INI DIPERBAIKI                              ==
                            // == formatStateUsing ditambahkan kembali untuk edit    ==
                            // ========================================================
                            Forms\Components\TextInput::make('jumlah_diajukan')
                                ->required()
                                ->prefix('Rp')
                                ->mask(RawJs::make('$money($input)'))
                                ->dehydrateStateUsing(function (?string $state): ?string {
                                    if ($state === null) {
                                        return null;
                                    }

                                    $cleanedState = preg_replace('/[^\d]/', '', $state);

                                    return $cleanedState;
                                }),

                            Forms\Components\DatePicker::make('tanggal_pengajuan')
                                ->default(now())
                                ->required(),
                            Forms\Components\Hidden::make('user_id')
                                ->default(auth()->id()),
                        ]),
                    // ... di dalam method form()

                    Forms\Components\Wizard\Step::make('Approval & Pencairan')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options(StatusPengajuanEnum::class)
                                ->disabled()
                                ->dehydrated(false),
                            Forms\Components\Textarea::make('catatan_approval')
                                ->disabled()
                                ->dehydrated(false),

                            // ===================================================================
                            // == GANTI SELURUH BLOK FileUpload DENGAN BLOK Placeholder INI     ==
                            // ===================================================================
                            // app/Filament/Resources/PengajuanDanaResource.php

                            // ... di dalam Wizard\Step 'Approval & Pencairan'
                            Forms\Components\Placeholder::make('bukti_transfer_viewer')
                                ->label('Bukti Transfer')
                                ->content(function ($record): ?HtmlString {
                                    // $record adalah model PengajuanDana yang sedang diedit
                                    if (blank($record?->bukti_transfer)) {
                                        return new HtmlString('<em>Belum ada file yang diunggah.</em>');
                                    }

                                    // =======================================================
                                    // == PERBAIKAN UTAMA ADA DI SINI                       ==
                                    // =======================================================

                                    // 1. Buat path relatif dari direktori public.
                                    //    $record->bukti_transfer berisi 'bukti-transfer/namafile.jpg'
                                    $relativePath = 'storage/' . $record->bukti_transfer;

                                    // 2. Gunakan helper asset() untuk membuat URL yang benar dan dinamis.
                                    //    Hasilnya akan menjadi /storage/bukti-transfer/namafile.jpg
                                    //    yang akan di-resolve dengan benar oleh browser.
                                    $correctUrl = asset($relativePath);

                                    // 3. Kembalikan HTML dengan URL yang sudah benar.
                                    return new HtmlString(
                                        // Link untuk mengunduh/melihat
                                        "<a href='{$correctUrl}' target='_blank' class='text-primary-600 hover:underline'>Lihat/Unduh File</a>" .
                                            // Pratinjau gambar jika file adalah gambar
                                            "<br><br><img src='{$correctUrl}' alt='Pratinjau Bukti Transfer' style='max-width: 300px; border-radius: 5px;'>"
                                    );
                                }),

                        ])
                        ->visibleOn('edit'),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('projectRequest.name')
                    ->label('Proyek')
                    ->searchable()
                    ->sortable()
                    ->limit(35), // Sedikit lebih panjang
                Tables\Columns\TextColumn::make('tujuan')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('jumlah_diajukan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diajukan Oleh')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pengajuan')
                    ->date('d M Y') // Format tanggal lebih mudah dibaca
                    ->sortable(),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    // Menggunakan mapWithKeys adalah cara yang paling eksplisit dan aman
                    ->options(
                        collect(StatusPengajuanEnum::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
                            ->all()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Aksi untuk menyetujui
                Tables\Actions\Action::make('setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(PengajuanDana $record) => $record->status === StatusPengajuanEnum::DIAJUKAN)
                    ->action(function (PengajuanDana $record) {
                        $record->update([
                            'status' => StatusPengajuanEnum::DISETUJUI,
                            'approved_by_id' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Pengajuan berhasil disetujui')->success()->send();
                    })
                    ->requiresConfirmation(), // Tambahkan konfirmasi

                // Aksi untuk menolak
                Tables\Actions\Action::make('tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(PengajuanDana $record) => $record->status === StatusPengajuanEnum::DIAJUKAN)
                    ->form([
                        Forms\Components\Textarea::make('catatan_approval')->label('Alasan Penolakan')->required(),
                    ])
                    ->action(function (PengajuanDana $record, array $data) {
                        $record->update([
                            'status' => StatusPengajuanEnum::DITOLAK,
                            'approved_by_id' => auth()->id(),
                            'approved_at' => now(),
                            'catatan_approval' => $data['catatan_approval'],
                        ]);
                        Notification::make()->title('Pengajuan telah ditolak')->danger()->send();
                    }),

                // Aksi untuk upload bukti transfer (pencairan)
                Tables\Actions\Action::make('cairkan')
                    ->icon('heroicon-o-receipt-refund')
                    ->color('info')
                    ->visible(fn(PengajuanDana $record) => $record->status === StatusPengajuanEnum::DISETUJUI)
                    ->form([
                        Forms\Components\FileUpload::make('bukti_transfer')
                            ->disk('public')
                            ->directory('bukti-transfer')
                            ->label('Unggah Bukti Transfer')
                            ->required(),

                        // BARU: Tambahkan field catatan
                        Forms\Components\Textarea::make('catatan_approval')
                            ->label('Catatan Approval / Pencairan')
                            ->rows(3) // Opsional: untuk mengatur tinggi default textarea
                            ->helperText('Catatan ini bersifat opsional.'), // Opsional: Memberi petunjuk
                    ])
                    ->action(function (PengajuanDana $record, array $data) {
                        $record->update([
                            'status' => StatusPengajuanEnum::DICAIRKAN,
                            'bukti_transfer' => $data['bukti_transfer'],
                            'dicairkan_at' => now(),

                            // BARU: Simpan catatan ke database
                            'catatan_approval' => $data['catatan_approval'],
                        ]);

                        Notification::make()->title('Dana telah ditandai cair')->info()->send();
                    }),
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
            'index' => Pages\ListPengajuanDanas::route('/'),
            'create' => Pages\CreatePengajuanDana::route('/create'),
            'edit' => Pages\EditPengajuanDana::route('/{record}/edit'),
        ];
    }
}
