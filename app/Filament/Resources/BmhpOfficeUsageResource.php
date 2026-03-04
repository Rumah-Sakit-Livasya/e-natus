<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BmhpCluster;
use App\Filament\Resources\BmhpOfficeUsageResource\Pages;
use App\Models\Bmhp;
use App\Models\BmhpOfficeUsage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BmhpOfficeUsageResource extends Resource
{
    protected static ?string $model = BmhpOfficeUsage::class;

    protected static ?string $cluster = BmhpCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Pemakaian Kantor';
    protected static ?string $pluralModelLabel = 'Pemakaian Kantor';

    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('used_at')
                    ->label('Tanggal Pakai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bmhp.name')
                    ->label('BMHP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_used')
                    ->label('Qty Pakai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diinput Oleh')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diinput At')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([])
            ->headerActions([
                Action::make('createMultipleUsage')
                    ->label('Catat Pemakaian Kantor')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->form([
                        Repeater::make('usage_items')
                            ->label('Item Pemakaian')
                            ->schema([
                                Select::make('bmhp_id')
                                    ->label('Pilih BMHP')
                                    ->options(fn() => Bmhp::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->required()
                                    ->native(false),
                                TextInput::make('qty_used')
                                    ->label('Qty Pakai')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                                DatePicker::make('used_at')
                                    ->label('Tanggal Pakai')
                                    ->default(now())
                                    ->required(),
                                Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->dehydrated(true),
                            ])
                            ->columns(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Item'),
                    ])
                    ->action(function (array $data): void {
                        $items = $data['usage_items'] ?? [];

                        if (!is_array($items) || count($items) === 0) {
                            throw ValidationException::withMessages([
                                'usage_items' => 'Minimal 1 item pemakaian wajib diisi.',
                            ]);
                        }

                        DB::transaction(function () use ($items) {
                            foreach ($items as $index => $item) {
                                $bmhpId = $item['bmhp_id'] ?? null;
                                $qtyUsed = (int) ($item['qty_used'] ?? 0);

                                if (!$bmhpId) {
                                    throw ValidationException::withMessages([
                                        "usage_items.{$index}.bmhp_id" => 'BMHP wajib dipilih.',
                                    ]);
                                }

                                if ($qtyUsed <= 0) {
                                    throw ValidationException::withMessages([
                                        "usage_items.{$index}.qty_used" => 'Qty pakai harus lebih dari 0.',
                                    ]);
                                }

                                $bmhp = Bmhp::query()->lockForUpdate()->find($bmhpId);
                                if (!$bmhp) {
                                    throw ValidationException::withMessages([
                                        "usage_items.{$index}.bmhp_id" => 'BMHP tidak ditemukan.',
                                    ]);
                                }

                                if ((int) $bmhp->stok_sisa < $qtyUsed) {
                                    throw ValidationException::withMessages([
                                        "usage_items.{$index}.qty_used" => "Stok {$bmhp->name} tidak cukup. Stok saat ini: {$bmhp->stok_sisa}.",
                                    ]);
                                }

                                $bmhp->decrement('stok_sisa', $qtyUsed);

                                BmhpOfficeUsage::create([
                                    'bmhp_id' => $bmhp->id,
                                    'qty_used' => $qtyUsed,
                                    'used_at' => $item['used_at'] ?? now()->toDateString(),
                                    'location' => $item['location'] ?? null,
                                    'keterangan' => $item['keterangan'] ?? null,
                                    'user_id' => $item['user_id'] ?? auth()->id(),
                                ]);
                            }
                        });

                        Notification::make()
                            ->success()
                            ->title('Pemakaian kantor berhasil dicatat')
                            ->body('Stok BMHP sudah dikurangi sesuai pemakaian kantor.')
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhpOfficeUsages::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->can('view bmhp office usage');
    }
}
