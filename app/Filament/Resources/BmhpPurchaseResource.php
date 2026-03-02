<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BmhpPurchaseResource\Pages;
use App\Models\GeneralSetting;
use App\Models\Bmhp;
use App\Models\BmhpPurchase;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BmhpPurchaseResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\BmhpCluster::class;

    protected static ?string $model = BmhpPurchase::class;
    protected static ?string $modelLabel = 'Pembelian BHP';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Pembelian BHP';
    protected static ?string $pluralModelLabel = 'Pembelian BHP';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_pembelian')
                    ->label('Tanggal Pembelian')
                    ->required(),

                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
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
                    ->createOptionUsing(function (array $data): int {
                        $supplier = Supplier::create([
                            'name' => $data['name'],
                            'address' => $data['address'] ?? null,
                            'phone' => $data['phone'] ?? null,
                        ]);

                        return $supplier->id;
                    })
                    ->nullable(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('nota_pembelian')
                    ->label('Upload Nota Pembelian')
                    ->disk('public')
                    ->directory('bmhp-purchase-nota')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->downloadable()
                    ->openable()
                    ->nullable()
                    ->columnSpanFull()
                    ->helperText('Opsional. Upload nota dalam format PDF/JPG/PNG/WEBP.'),

                Forms\Components\Repeater::make('items')
                    ->label('Item Pembelian')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('bmhp_id')
                            ->label('BMHP')
                            ->options(fn() => Bmhp::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->native(false)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $bmhp = $state ? Bmhp::find($state) : null;
                                $set('pcs_per_unit_snapshot', $bmhp?->pcs_per_unit);

                                $qty = (int) ($get('qty') ?? 0);
                                $purchaseType = (string) ($get('purchase_type') ?? 'pcs');
                                $pcsPerUnit = (int) ($bmhp?->pcs_per_unit ?? 0);

                                $totalPcs = 0;
                                if ($purchaseType === 'pcs') {
                                    $totalPcs = $qty;
                                } else {
                                    $totalPcs = $pcsPerUnit > 0 ? $qty * $pcsPerUnit : 0;
                                }

                                $set('total_pcs', $totalPcs);
                            }),

                        Forms\Components\Select::make('purchase_type')
                            ->label('Beli Per')
                            ->options(function (Forms\Get $get) {
                                $bmhpId = $get('bmhp_id');
                                $satuan = 'Kemasan';
                                if ($bmhpId) {
                                    $bmhp = Bmhp::find($bmhpId);
                                    if ($bmhp && $bmhp->satuan) {
                                        $satuan = $bmhp->satuan;
                                    }
                                }
                                return [
                                    'unit' => $satuan . ' (unit)',
                                    'pcs' => 'Pcs',
                                ];
                            })
                            ->default('pcs')
                            ->required()
                            ->reactive()
                            ->native(false)
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty = (int) ($get('qty') ?? 0);
                                $purchaseType = (string) ($state ?? 'pcs');
                                $pcsPerUnit = (int) ($get('pcs_per_unit_snapshot') ?? 0);

                                $totalPcs = 0;
                                if ($purchaseType === 'pcs') {
                                    $totalPcs = $qty;
                                } else {
                                    $totalPcs = $pcsPerUnit > 0 ? $qty * $pcsPerUnit : 0;
                                }

                                $set('total_pcs', $totalPcs);
                            }),

                        Forms\Components\TextInput::make('qty')
                            ->label('Qty')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty = (int) ($state ?? 0);
                                $purchaseType = (string) ($get('purchase_type') ?? 'pcs');
                                $pcsPerUnit = (int) ($get('pcs_per_unit_snapshot') ?? 0);

                                $totalPcs = 0;
                                if ($purchaseType === 'pcs') {
                                    $totalPcs = $qty;
                                } else {
                                    $totalPcs = $pcsPerUnit > 0 ? $qty * $pcsPerUnit : 0;
                                }

                                $set('total_pcs', $totalPcs);
                            }),

                        Forms\Components\TextInput::make('pcs_per_unit_snapshot')
                            ->label('Isi/Kemasan (pcs)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->required(fn(callable $get): bool => (string) ($get('purchase_type') ?? 'pcs') === 'unit')
                            ->minValue(1)
                            ->visible(fn(callable $get) => (string) ($get('purchase_type') ?? 'pcs') === 'unit'),

                        Forms\Components\TextInput::make('total_pcs')
                            ->label('Total Pcs')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true),

                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Beli')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input)'))
                            ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : 0)
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->helperText('Total harga untuk item ini'),

                        Forms\Components\Checkbox::make('is_checked')
                            ->label('Sesuai Nota')
                            ->default(true)
                            ->helperText('Centang jika item ini benar-benar dibeli sesuai nota.'),
                    ])
                    ->columns(6)
                    ->minItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $query->whereHas('items');
            })
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pembelian')->label('Tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Harga Beli')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->getStateUsing(function (BmhpPurchase $record): float {
                        return $record->items->sum('harga');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')->limit(40)->wrap(),
                Tables\Columns\TextColumn::make('approved_at')->label('Approved At')->dateTime('d M Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('approver.name')->label('Disetujui Oleh')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(fn() => GeneralSetting::isBmhpPurchaseApprovalRequired() ? 'Approve' : 'Selesaikan Pembelian')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function (BmhpPurchase $record) {
                        if ($record->status !== 'pending') return false;

                        if (GeneralSetting::isBmhpPurchaseApprovalRequired()) {
                            return auth()->user()->can('approve bmhp');
                        }

                        // If approval not required, anyone who can view property can probably settle it
                        return true;
                    })
                    ->action(function (BmhpPurchase $record) {
                        DB::transaction(function () use ($record) {
                            $record->refresh();
                            if ($record->status !== 'pending') {
                                Log::info('Purchase #' . $record->id . ' is not pending, status: ' . $record->status);
                                return;
                            }

                            $record->loadMissing('items.bmhp');
                            Log::info('Loaded ' . $record->items->count() . ' items for purchase #' . $record->id);

                            foreach ($record->items as $item) {
                                if (!(bool) ($item->is_checked ?? true)) {
                                    Log::info('Skipping item ID: ' . $item->id . ' because is_checked is false');
                                    continue;
                                }

                                $pcs = (int) ($item->total_pcs ?? 0);
                                Log::info('Processing item ID: ' . $item->id . ', BMHP ID: ' . $item->bmhp_id . ', Total PCS: ' . $pcs);

                                if ($pcs <= 0) {
                                    Log::info('Skipping item due to zero or negative PCS: ' . $pcs);
                                    continue;
                                }

                                if (!$item->bmhp) {
                                    Log::error('BMHP relation is NULL for item ID: ' . $item->id . ', BMHP ID: ' . $item->bmhp_id);
                                    continue;
                                }

                                $oldStock = $item->bmhp->stok_sisa;
                                $item->bmhp->increment('stok_sisa', $pcs);
                                Log::info('Updated BMHP #' . $item->bmhp_id . ' stock from ' . $oldStock . ' to ' . ($oldStock + $pcs) . ' (+' . $pcs . ')');
                            }

                            $record->update([
                                'status' => 'approved',
                                'approved_at' => now(),
                                'approved_by' => Auth::id(),
                            ]);
                            Log::info('Purchase #' . $record->id . ' approved successfully');
                        });

                        Notification::make()
                            ->success()
                            ->title('Pembelian di-approve')
                            ->body('Stok BMHP sudah ditambahkan untuk item yang dicentang sesuai nota.')
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn(BmhpPurchase $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBmhpPurchases::route('/'),
            'create' => Pages\CreateBmhpPurchase::route('/create'),
            'edit' => Pages\EditBmhpPurchase::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user && $user->can('view bmhp');
    }
}
