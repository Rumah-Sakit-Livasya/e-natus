<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BmhpResource;
use App\Models\Bmhp;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockBmhpWidget extends BaseWidget
{
    protected static ?string $heading = 'Peringatan Stok Minimum BMHP';

    protected int | string | array $columnSpan = 'full';

    // Urutan widget di dashboard, angka kecil akan tampil lebih atas
    protected static ?int $sort = 2;

    /**
     * Widget ini hanya akan ditampilkan jika ada item yang stoknya rendah.
     */
    // public static function canView(): bool
    // {
    //     // Cek apakah ada BMHP di mana stok sisa <= stok min, dan stok min > 0
    //     return Bmhp::query()
    //         ->where('min_stok', '>', 0)
    //         ->whereColumn('stok_sisa', '<=', 'min_stok')
    //         ->exists();
    // }

    public static function canView(): bool
    {
        // Paksa untuk selalu tampil
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query untuk mengambil data yang akan ditampilkan
                Bmhp::query()
                    ->where('min_stok', '>', 0)
                    ->whereColumn('stok_sisa', '<=', 'min_stok')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama BMHP'),
                TextColumn::make('stok_sisa')
                    ->label('Stok Sisa')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('min_stok')
                    ->label('Stok Minimum'),
            ])
            ->actions([
                // Tambahkan action untuk langsung menuju halaman edit item
                Tables\Actions\Action::make('Edit')
                    ->url(fn(Bmhp $record): string => BmhpResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil-square'),
            ]);
    }
}
