<?php

namespace App\Filament\Pages;

use App\Models\ProjectRequest;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaporanMargin extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.laporan-margin';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Margin';

    protected static ?string $title = 'Laporan Margin Keuntungan';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view laporan margin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProjectRequest::query()
                    ->with(['projectBmhp', 'rabOperasionalItems', 'rabFeeItems'])
                    ->where('status', '!=', 'rejected')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label('Klien')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nilai_invoice')
                    ->label('Nilai Invoice')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_biaya')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->state(function (ProjectRequest $record): float {
                        return $record->projectBmhp->sum('total') +
                               $record->rabOperasionalItems->sum('total') +
                               $record->rabFeeItems->sum('total');
                    }),

                TextColumn::make('margin')
                    ->label('Margin')
                    ->money('IDR')
                    ->state(function (ProjectRequest $record): float {
                        $totalBiaya = $record->projectBmhp->sum('total') +
                                      $record->rabOperasionalItems->sum('total') +
                                      $record->rabFeeItems->sum('total');

                        return ($record->nilai_invoice ?? 0) - $totalBiaya;
                    })
                    ->color(fn (string $state): string => (float) $state >= 0 ? 'success' : 'danger'),

                TextColumn::make('margin_percentage')
                    ->label('% Margin')
                    ->state(function (ProjectRequest $record): string {
                        $invoice = $record->nilai_invoice ?? 0;
                        if ($invoice == 0) {
                            return '0%';
                        }

                        $totalBiaya = $record->projectBmhp->sum('total') +
                                      $record->rabOperasionalItems->sum('total') +
                                      $record->rabFeeItems->sum('total');
                        $margin = $invoice - $totalBiaya;

                        return number_format(($margin / $invoice) * 100, 2).'%';
                    })
                    ->color(fn (string $state): string => (float) $state >= 0 ? 'success' : 'danger'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\Filter::make('start_period')->form([\Filament\Forms\Components\DatePicker::make('from')->label('Dari Tanggal'), \Filament\Forms\Components\DatePicker::make('until')->label('Sampai Tanggal')])->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('start_period', '>=', $date))->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('start_period', '<=', $date));
                }),
                \Filament\Tables\Filters\SelectFilter::make('client_id')
                    ->label('Klien')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'done' => 'Selesai',
                    ]),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('Laporan_Margin_'.date('Y-m-d'))
                            ->withColumns([
                                Column::make('name')->heading('Nama Proyek'),
                                Column::make('client.name')->heading('Klien'),
                                Column::make('nilai_invoice')->heading('Nilai Invoice'),
                                Column::make('total_biaya')
                                    ->heading('Total Biaya')
                                    ->formatStateUsing(fn ($record) => $record->projectBmhp->sum('total') + $record->rabOperasionalItems->sum('total') + $record->rabFeeItems->sum('total')),
                                Column::make('margin')
                                    ->heading('Margin')
                                    ->formatStateUsing(fn ($record) => ($record->nilai_invoice ?? 0) - ($record->projectBmhp->sum('total') + $record->rabOperasionalItems->sum('total') + $record->rabFeeItems->sum('total'))),
                            ]),
                    ]),
            ]);
    }
}
