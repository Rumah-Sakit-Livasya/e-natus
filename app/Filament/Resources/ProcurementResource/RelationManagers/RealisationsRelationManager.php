<?php

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;

class RealisationsRelationManager extends RelationManager
{
    protected static string $relationship = 'realisations'; // nama relasi di model Procurement

    protected static ?string $recordTitleAttribute = 'procurement_item_id';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('procurement_item_id')
                ->label('Item Pengadaan')
                ->options(function () {
                    $procurementId = $this->ownerRecord->id; // ambil id procurement dari parent record

                    $allItems = \App\Models\ProcurementItem::where('procurement_id', $procurementId);
                    $alreadyRealisedIds = \App\Models\Realisation::pluck('procurement_item_id')->toArray();

                    return $allItems->whereNotIn('id', $alreadyRealisedIds)->pluck('nama_barang', 'id');
                })
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'SELESAI' => 'SELESAI',
                    'TIDAK' => 'TIDAK',
                ])
                ->required(),

            Forms\Components\TextInput::make('harga_realisasi')
                ->numeric()
                ->required()
                ->minValue(0),

            Forms\Components\TextInput::make('qty_realisasi')
                ->required()
                ->minValue(0),

            Forms\Components\TextInput::make('satuan')
                ->required(),

            Forms\Components\Select::make('supplier_id')
                ->label('Supplier')
                ->relationship('supplier', 'name') // Asumsi field name di tabel suppliers
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Supplier')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\TextInput::make('address')
                        ->label('Alamat')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(50),
                ])
                ->label('Tambah Supplier Baru')
                ->required(),


            Forms\Components\TextInput::make('persentase_hemat')
                ->label('Persentase Hemat')
                ->numeric()
                ->disabled()
                ->dehydrated(false)
                ->suffix('%')
                ->reactive()
                ->afterStateHydrated(function ($set, $get, $state, $record) {
                    if ($record && $record->procurementItem) {
                        $hargaPengajuan = $record->procurementItem->harga_pengajuan ?? 0;
                        $hargaRealisasi = $record->harga_realisasi ?? 0;

                        if ($hargaPengajuan > 0) {
                            $persentase = (($hargaPengajuan - $hargaRealisasi) / $hargaPengajuan) * 100;
                            $set('persentase_hemat', round($persentase, 2));
                        } else {
                            $set('persentase_hemat', 0);
                        }
                    }
                }),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('procurementItem.nama_barang')->label('Nama Barang'),
            Tables\Columns\TextColumn::make('status'),
            Tables\Columns\TextColumn::make('harga_realisasi')->money('idr', true),
            Tables\Columns\TextColumn::make('qty_realisasi'),
            Tables\Columns\TextColumn::make('satuan'),
            Tables\Columns\TextColumn::make('supplier.name'),
            Tables\Columns\TextColumn::make('persentase_hemat')->suffix('%'),
        ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
