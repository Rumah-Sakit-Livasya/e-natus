<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RealisationResource\Pages;
use App\Filament\Resources\RealisationResource\RelationManagers;
use App\Models\Realisation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RealisationResource extends Resource
{
    protected static ?string $model = Realisation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Realisasi';
    protected static ?string $navigationLabel = 'List Realisasi';
    protected static ?string $pluralModelLabel = 'List Realisasi';

    public static ?int $filterProcurementId = null;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('procurement_item_id')
                ->label('Item Pengadaan')
                ->relationship('procurementItem', 'nama_barang')
                ->searchable()
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
                ->relationship('supplier', 'name') // Asumsi field nama di tabel suppliers
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Supplier')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('kontak')
                        ->label('Kontak') // opsional
                        ->maxLength(255),
                ])
                ->label('Tambah Supplier Baru')
                ->required(),
            Forms\Components\TextInput::make('persentase_hemat')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->suffix('%'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('procurementItem.nama_barang')->label('Nama Barang'),
                Tables\Columns\TextColumn::make('procurementItem.procurement.tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date(),
                Tables\Columns\TextColumn::make('procurementItem.procurement.keterangan')
                    ->label('Keterangan')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('harga_realisasi')->money('idr', true),
                Tables\Columns\TextColumn::make('qty_realisasi'),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('supplier.name'),
                Tables\Columns\TextColumn::make('persentase_hemat')->suffix('%'),
            ])
            ->groups([
                Tables\Grouping\Group::make('procurementItem.procurement.keterangan')
                    ->label('Keterangan')
                    ->collapsible(),
                Tables\Grouping\Group::make('procurementItem.procurement.tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date()
                    ->collapsible(),
            ])
            ->defaultGroup('procurementItem.procurement.keterangan') // Grup default saat load
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::$filterProcurementId) {
            $query->whereHas('procurementItem', function (Builder $q) {
                $q->where('procurement_id', static::$filterProcurementId);
            });
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRealisations::route('/'),
            'create' => Pages\CreateRealisation::route('/create'),
            'edit' => Pages\EditRealisation::route('/{record}/edit'),
        ];
    }
}
