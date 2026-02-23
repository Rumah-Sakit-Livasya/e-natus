<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpResource\Pages;
use App\Models\Bmhp;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BmhpResource extends Resource
{
    protected static ?string $model = Bmhp::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'BMHP';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nama BMHP')->required(),
            Select::make('satuan')
                ->label('Satuan')
                ->options(function () {
                    // Ambil semua satuan yang sudah ada dari database
                    return \App\Models\Bmhp::whereNotNull('satuan')
                        ->distinct()
                        ->pluck('satuan', 'satuan')
                        ->toArray();
                })
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nama Satuan')
                        ->required()
                        ->maxLength(50),
                ])
                ->createOptionUsing(function (array $data) {
                    // Simpan satuan baru ke database
                    $newSatuan = $data['name'];

                    // Cek apakah sudah ada
                    if (!\App\Models\Bmhp::where('satuan', $newSatuan)->exists()) {
                        // Buat record dummy untuk satuan baru
                        \App\Models\Bmhp::create([
                            'name' => '[DUMMY] ' . $newSatuan,
                            'satuan' => $newSatuan,
                            'stok_awal' => 0,
                            'stok_sisa' => 0,
                            'harga_satuan' => 0,
                            'min_stok' => 0,
                        ]);
                    }

                    return $newSatuan;
                })
                ->preload()
                ->helperText('Pilih satuan yang ada atau buat baru'),
            TextInput::make('harga_satuan')->numeric()->label('Harga Satuan')->prefix('Rp'),
            TextInput::make('stok_awal')->numeric()->label('Stok Awal')->default(0),
            TextInput::make('stok_sisa')
                ->numeric()
                ->label('Stok Sisa')
                ->default(0)
                ->disabledOn('edit'),
            TextInput::make('min_stok')
                ->numeric()
                ->label('Stok Minimum')
                ->default(0)
                ->helperText('Stok akan ditandai jika sisa stok sama atau di bawah angka ini.'),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('satuan')->label('Satuan'),
                TextColumn::make('stok_awal')->label('Stok Awal')->sortable(),
                TextColumn::make('stok_sisa')->label('Stok Sisa')->sortable()
                    ->color(function ($state, Bmhp $record): string {
                        if ($record->min_stok > 0 && $state <= $record->min_stok) {
                            return 'danger'; // Merah jika di bawah atau sama dengan stok min
                        }
                        return 'success'; // Hijau jika aman
                    }),
                TextColumn::make('harga_satuan')->money('idr', true)->label('Harga'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ExportAction::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make('bmhp')
                            ->fromModel()
                            ->withColumns([
                                Column::make('name')->heading('Nama BMHP'),
                                Column::make('satuan')->heading('Satuan'),
                                Column::make('stok_awal')->heading('Stok Awal'),
                                Column::make('stok_sisa')->heading('Stok Sisa'),
                                Column::make('harga_satuan')->heading('Harga Satuan'),
                                Column::make('min_stok')->heading('Stok Minimum'),
                            ])
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make('bmhp_selected')
                            ->fromModel()
                            ->withColumns([
                                Column::make('name')->heading('Nama BMHP'),
                                Column::make('satuan')->heading('Satuan'),
                                Column::make('stok_awal')->heading('Stok Awal'),
                                Column::make('stok_sisa')->heading('Stok Sisa'),
                                Column::make('harga_satuan')->heading('Harga Satuan'),
                                Column::make('min_stok')->heading('Stok Minimum'),
                            ])
                    ])
            ]);
    }


    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\BmhpResource\RelationManagers\StockOpnamesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhps::route('/'),
            'create' => Pages\CreateBmhp::route('/create'),
            'edit' => Pages\EditBmhp::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view bmhp');
    }
}
