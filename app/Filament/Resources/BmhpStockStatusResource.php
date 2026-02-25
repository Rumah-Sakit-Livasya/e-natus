<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpStockStatusResource\Pages;
use App\Models\Bmhp;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BmhpStockStatusResource extends Resource
{
    protected static ?string $model = Bmhp::class;

    protected static ?string $cluster = \App\Filament\Clusters\BmhpCluster::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Status Stok BHP';
    protected static ?string $pluralModelLabel = 'Status Stok BHP';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('pcs_per_unit')
                    ->label('Isi (pcs)')
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_stok')
                    ->label('Stok Minimum')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stok_sisa')->label('Stok Sisa')->sortable()
                    ->color(function ($state, Bmhp $record): string {
                        if ($state <= 0) {
                            return 'danger'; // Merah jika stok habis
                        }
                        if ($record->min_stok > 0 && $state <= $record->min_stok) {
                            return 'warning'; // Kuning jika di bawah atau sama dengan stok min
                        }
                        return 'success'; // Hijau jika aman
                    }),

                Tables\Columns\TextColumn::make('stock_status')
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
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('stock_status')
                    ->label('Filter Status')
                    ->options([
                        'aman' => 'Aman',
                        'menipis' => 'Menipis',
                        'habis' => 'Habis',
                    ])
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
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhpStockStatuses::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->can('view bmhp');
    }
}
