<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpStockOpnameResource\Pages;
use App\Models\Bmhp;
use App\Models\BmhpStockOpname;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class BmhpStockOpnameResource extends Resource
{
    protected static ?string $model = BmhpStockOpname::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Riwayat Stock Opname';
    protected static ?string $pluralModelLabel = 'Riwayat Stock Opname';

    public static function form(Form $form): Form
    {
        // Form ini tetap ada untuk Aksi Edit
        return $form
            ->schema([
                Select::make('bmhp_id')
                    ->relationship('bmhp', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit'), // Tidak bisa mengubah item saat edit
                TextInput::make('stok_fisik')
                    ->numeric()
                    ->required()
                    ->disabledOn('edit'),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bmhp.name')
                    ->label('Nama BMHP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stok_fisik')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Diperbarui Oleh')
                    ->searchable()
                    ->sortable()
                    ->default('N/A'),
                TextColumn::make('keterangan')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Tanggal Opname')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ==========================================================
            // ▼▼▼ BAGIAN BARU DITAMBAHKAN DI SINI ▼▼▼
            // ==========================================================
            ->headerActions([
                Action::make('createMultiple')
                    ->label('Buat Stock Opname Massal')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->form([
                        Repeater::make('opname_items')
                            ->label('Item Stock Opname')
                            ->schema([
                                Select::make('bmhp_id')
                                    ->label('Pilih BMHP')
                                    ->options(Bmhp::query()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                TextInput::make('stok_fisik')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('keterangan')
                                    ->label('Keterangan (Opsional)'),
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambah Item BMHP'),
                    ])
                    ->action(function (array $data) {
                        // 1. Dapatkan data dari repeater
                        $items = $data['opname_items'] ?? [];

                        if (empty($items)) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak ada item')
                                ->body('Anda harus menambahkan setidaknya satu item untuk disimpan.')
                                ->send();
                            return;
                        }

                        // 2. Gunakan Transaction untuk menjaga konsistensi data
                        DB::transaction(function () use ($items) {
                            foreach ($items as $item) {
                                // 3. Buat record stock opname baru
                                BmhpStockOpname::create([
                                    'bmhp_id' => $item['bmhp_id'],
                                    'stok_fisik' => $item['stok_fisik'],
                                    'keterangan' => $item['keterangan'],
                                ]);

                                // 4. Update stok_sisa di tabel master Bmhp
                                Bmhp::where('id', $item['bmhp_id'])->update([
                                    'stok_sisa' => $item['stok_fisik']
                                ]);
                            }
                        });

                        // 5. Kirim notifikasi sukses
                        Notification::make()
                            ->success()
                            ->title('Stock opname berhasil disimpan')
                            ->body('Sebanyak ' . count($items) . ' item BMHP telah diperbarui stoknya.')
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhpStockOpnames::route('/'),
        ];
    }
}
