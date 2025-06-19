<?php

namespace App\Filament\Resources\ProcurementResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class ProcurementItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'nama_barang';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_barang')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('unit')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('harga_pengajuan')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('qty_pengajuan')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('satuan')
                ->required()
                ->maxLength(20),
            Forms\Components\TextInput::make('status')
                ->maxLength(50),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_barang')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('harga_pengajuan')->money('idr', true),
                Tables\Columns\TextColumn::make('qty_pengajuan'),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('jumlah_pengajuan')->money('idr', true),
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
