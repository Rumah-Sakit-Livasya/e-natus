<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpResource\Pages;
use App\Models\Bmhp;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BmhpResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\BmhpCluster::class;

    protected static ?string $slug = 'bhp';

    protected static ?string $modelLabel = 'Barang Habis Pakai';
    protected static ?string $pluralModelLabel = 'Barang Habis Pakai';
    protected static ?string $model = Bmhp::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'BHP';



    protected static ?int $navigationSort = 1;

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
                ->reactive()
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
                            'stok_sisa' => 0,
                            'min_stok' => 0,
                        ]);
                    }

                    return $newSatuan;
                })
                ->preload()
                ->native(false)
                ->helperText('Pilih satuan yang ada atau buat baru'),
            Checkbox::make('has_multiple_pcs')
                ->label('Satuan ini mengandung beberapa pcs')
                ->reactive()
                ->dehydrated(false)
                ->afterStateHydrated(function (Checkbox $component, Get $get): void {
                    $pcsPerUnit = (int) ($get('pcs_per_unit') ?? 0);
                    $component->state($pcsPerUnit > 1);
                })
                ->afterStateUpdated(function (Set $set, bool $state): void {
                    if (! $state) {
                        $set('pcs_per_unit', null);
                    }
                })
                ->helperText('Centang jika satu satuan mengandung lebih dari 1 pcs (misal: 1 box = 10 pcs)'),
            TextInput::make('pcs_per_unit')
                ->numeric()
                ->label('Isi per Satuan (pcs)')
                ->minValue(1)
                ->default(null)
                ->visible(function (Get $get): bool {
                    return (bool) ($get('has_multiple_pcs') ?? false);
                })
                ->required(function (Get $get): bool {
                    return (bool) ($get('has_multiple_pcs') ?? false);
                })
                ->dehydratedWhenHidden()
                ->helperText('Jika satuan ini mengandung beberapa pcs, isi berapa pcs dalam satu satuan.'),
            TextInput::make('stok_sisa')
                ->numeric()
                ->label('Stok Sisa (pcs)')
                ->default(0)
                ->helperText('Isi stok sisa dalam pcs. Contoh: jika sisa 2 pack dan 1 pack = 6 pcs, isi 12.')
                ->disabledOn('edit'),
            TextInput::make('min_stok')
                ->numeric()
                ->label(fn(Get $get): string => 'Stok Minimum' . ($get('satuan') ? ' (' . $get('satuan') . ')' : ''))
                ->default(0)
                ->helperText(function (Get $get): string {
                    $satuan = (string) ($get('satuan') ?: 'satuan');
                    return "Batas minimum dihitung per {$satuan}. Sistem menandai jika sisa <= nilai ini.";
                }),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('satuan')->label('Satuan'),
                TextColumn::make('pcs_per_unit')->label('Isi (pcs)')->sortable(),
                TextColumn::make('stok_sisa')->label('Stok Sisa (pcs)')->sortable()
                    ->color(function ($state, Bmhp $record): string {
                        if ($state <= 0) {
                            return 'danger'; // Merah jika stok habis
                        }
                        if ($record->min_stok > 0 && $state <= $record->min_stok) {
                            return 'warning'; // Kuning jika di bawah atau sama dengan stok min
                        }
                        return 'success'; // Hijau jika aman
                    }),
                TextColumn::make('stock_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Bmhp $record): string {
                        if ((int) $record->stok_sisa <= 0) {
                            return 'HABIS';
                        }

                        if ((int) $record->min_stok > 0 && (int) $record->stok_sisa <= (int) $record->min_stok) {
                            return 'MENIPIS';
                        }

                        return 'AMAN';
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'HABIS' => 'danger',
                            'MENIPIS' => 'warning',
                            default => 'success',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Filter Status')
                    ->options([
                        'aman' => 'Aman',
                        'menipis' => 'Menipis',
                        'habis' => 'Habis',
                    ])
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;
                        if (!$value) {
                            return $query;
                        }

                        return match ($value) {
                            'habis' => $query->where('stok_sisa', '<=', 0),
                            'menipis' => $query->where('stok_sisa', '>', 0)->where('min_stok', '>', 0)->whereColumn('stok_sisa', '<=', 'min_stok'),
                            default => $query->where(function (Builder $q) {
                                $q->where('stok_sisa', '>', 0)
                                    ->where(function (Builder $q2) {
                                        $q2->where('min_stok', '<=', 0)
                                            ->orWhereColumn('stok_sisa', '>', 'min_stok');
                                    });
                            }),
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        ExcelExport::make('bmhp_selected')
                            ->fromModel()
                            ->withColumns([
                                Column::make('name')->heading('Nama BMHP'),
                                Column::make('satuan')->heading('Satuan'),
                                Column::make('pcs_per_unit')->heading('Isi per Kemasan (pcs)'),
                                Column::make('stok_sisa')->heading('Stok Sisa'),
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
        return $user->can('view bmhp master');
    }
}
