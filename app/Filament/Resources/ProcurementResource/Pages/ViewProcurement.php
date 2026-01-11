<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\ProcurementResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\ProcurementItem;

class ViewProcurement extends ViewRecord implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = ProcurementResource::class;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Models\ProcurementItem::query()->where('procurement_id', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nama_barang')->label('Nama Barang'),
            TextColumn::make('unit')->label('Unit'),
            TextColumn::make('harga_pengajuan')->label('Harga')->money('IDR', true),
            TextColumn::make('qty_pengajuan')->label('Qty'),
            TextColumn::make('satuan')->label('Satuan'),
            TextColumn::make('jumlah')
                ->label('Jumlah')
                ->getStateUsing(fn($record) => $record->harga_pengajuan * $record->qty_pengajuan)
                ->money('IDR', true),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'success' => 'Terealisasi',
                    'danger' => 'Tidak Terealisasi',
                    'gray' => fn($state) => is_null($state),
                ]),
        ];
    }
}
