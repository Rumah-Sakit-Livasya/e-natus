<?php

namespace App\Filament\Resources\BmhpResource\RelationManagers;

use App\Models\BmhpStockOpname;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StockOpnamesRelationManager extends RelationManager
{
    protected static string $relationship = 'stockOpnames';
    protected static ?string $title = 'Riwayat Stock Opname';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('stok_fisik')
                    ->label('Stok Fisik Saat Ini')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan (Opsional)')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stok_fisik')
            ->columns([
                Tables\Columns\TextColumn::make('stok_fisik'),
                Tables\Columns\TextColumn::make('keterangan')->limit(50),

                // ▼▼▼ TAMBAHKAN KOLOM INI UNTUK MENAMPILKAN NAMA USER ▼▼▼
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->default('N/A'), // Tampil jika user_id null

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Opname')
                    ->dateTime('d M Y, H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // ▼▼▼ GANTI ->after() DENGAN ->mutateFormDataUsing() ▼▼▼
                    ->mutateFormDataUsing(function (array $data): array {
                        // Secara otomatis tambahkan user_id dari user yang sedang login
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->after(function (BmhpStockOpname $record) {
                        // Logika update stok sisa tetap di sini
                        $this->ownerRecord->update([
                            'stok_sisa' => $record->stok_fisik
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
