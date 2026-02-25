<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpStockOpnameResource\Pages;
use App\Models\Bmhp;
use App\Models\BmhpStockOpname;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn; // <-- Gunakan BadgeColumn untuk status
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;

class BmhpStockOpnameResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\BmhpCluster::class;

    protected static ?string $model = BmhpStockOpname::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Riwayat Stock Opname';
    protected static ?string $pluralModelLabel = 'Riwayat Stock Opname';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form ini digunakan untuk halaman Edit
                Select::make('bmhp_id')->relationship('bmhp', 'name')->disabled(),
                TextInput::make('stok_fisik')->numeric()->disabled(),
                Textarea::make('keterangan')->columnSpanFull(),
                TextInput::make('status')->disabled(), // Tampilkan status, tapi tidak bisa diubah
                Hidden::make('user_id')->default(fn() => auth()->id())->dehydrated(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bmhp.name')->label('Nama BMHP')->searchable()->sortable(),
                TextColumn::make('stok_fisik')->sortable(),

                // ==========================================================
                // ▼▼▼ PERUBAHAN 1: TAMPILKAN KOLOM STATUS ▼▼▼
                // ==========================================================
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected', // Opsional, jika ada status reject
                    ])
                    ->sortable(),

                TextColumn::make('user.name')->label('Dibuat Oleh')->searchable()->sortable(),
                TextColumn::make('keterangan')->limit(50),
                TextColumn::make('created_at')->label('Tanggal Pengajuan')->dateTime('d M Y, H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // ==========================================================
                // ▼▼▼ PERUBAHAN 2: TAMBAHKAN TOMBOL APPROVE ▼▼▼
                // ==========================================================
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation() // Minta konfirmasi sebelum approve
                    ->action(function (BmhpStockOpname $record) {
                        if ($record->status !== 'pending') {
                            Notification::make()->warning()->title('Sudah Diproses')->body('Pengajuan ini sudah diproses sebelumnya.')->send();
                            return;
                        }

                        DB::transaction(function () use ($record) {
                            // 1. Update stok di tabel master BMHP
                            $record->bmhp->update(['stok_sisa' => $record->stok_fisik]);

                            // 2. Update status pengajuan menjadi 'approved'
                            $record->update(['status' => 'approved']);
                        });

                        Notification::make()->success()->title('Berhasil Disetujui')->body('Stok BMHP telah berhasil diperbarui.')->send();
                    })
                    // Tombol ini hanya muncul jika statusnya 'pending'
                    ->visible(fn(BmhpStockOpname $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('createMultiple')
                    ->label('Buat Pengajuan Stock Opname') // Ganti label
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Repeater::make('opname_items')
                            ->label('Item Stock Opname')
                            ->schema([
                                Select::make('bmhp_id')->label('Pilih BMHP')->options(Bmhp::query()->pluck('name', 'id'))->searchable()->required(),
                                TextInput::make('stok_fisik')->numeric()->required(),
                                TextInput::make('keterangan')->label('Keterangan (Opsional)'),
                                Hidden::make('user_id')->default(fn() => auth()->id())->dehydrated(true),
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambah Item BMHP'),
                    ])
                    ->action(function (array $data) {
                        $items = $data['opname_items'] ?? [];
                        if (empty($items)) {
                            // ... notifikasi jika kosong
                            return;
                        }

                        // ==========================================================
                        // ▼▼▼ PERUBAHAN 3: LOGIKA PEMBUATAN HANYA MEMBUAT PENGAJUAN ▼▼▼
                        // ==========================================================
                        foreach ($items as $item) {
                            // Hanya buat record baru dengan status default 'pending'
                            // Tidak ada lagi update ke stok master di sini!
                            BmhpStockOpname::create([
                                'bmhp_id' => $item['bmhp_id'],
                                'stok_fisik' => $item['stok_fisik'],
                                'keterangan' => $item['keterangan'] ?? null,
                                'user_id' => $item['user_id'] ?? auth()->id(),
                                'status' => 'pending', // Eksplisit set status
                            ]);
                        }

                        Notification::make()->success()->title('Pengajuan berhasil dibuat')->body('Pengajuan stock opname Anda telah dikirim untuk persetujuan.')->send();
                    }),
            ]);
    }

    // ==========================================================
    // ▼▼▼ PERUBAHAN 4: TAMBAHKAN HALAMAN EDIT AGAR URL NOTIFIKASI VALID ▼▼▼
    // ==========================================================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhpStockOpnames::route('/'),
            'edit' => Pages\EditBmhpStockOpname::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return $user && $user->can('view stock opname');
    }
}
