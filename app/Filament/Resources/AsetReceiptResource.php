<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsetReceiptResource\Pages;
use App\Models\AsetReceipt;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\{
    Repeater,
    Select,
    TextInput,
    DatePicker
};
use Illuminate\Database\Eloquent\Builder;

class AsetReceiptResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\AsetCluster::class;

    protected static ?string $model = AsetReceipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Pengadaan';
    protected static ?string $label = 'Serah Terima Aset';
    protected static ?string $pluralLabel = 'Serah Terima Aset';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('procurement_id')
                ->label('Pengadaan')
                ->relationship('procurement', 'keterangan')
                ->searchable()
                ->preload()
                ->reactive() // penting agar Placeholder bisa update
                ->required(),

            Forms\Components\Placeholder::make('preview')
                ->label(false)
                ->content(function (callable $get) {
                    $procurement = \App\Models\Procurement::with('items')->find($get('procurement_id'));
                    if (!$procurement) return '';

                    $tanggal = \Carbon\Carbon::parse($procurement->tanggal_pengajuan)->translatedFormat('d F Y');

                    $html = <<<HTML
                                <div class="space-y-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div><strong>Tanggal:</strong> {$tanggal}</div>
                                        <div><strong>Keterangan:</strong> {$procurement->keterangan}</div>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
                                            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                                <tr>
                                                    <th class="px-4 py-2 border-b dark:border-gray-700">Nama Barang</th>
                                                    <th class="px-4 py-2 border-b dark:border-gray-700">Qty</th>
                                                    <th class="px-4 py-2 border-b dark:border-gray-700">Satuan</th>
                                                    <th class="px-4 py-2 border-b dark:border-gray-700">Harga Satuan</th>
                                                    <th class="px-4 py-2 border-b dark:border-gray-700">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                HTML;

                    foreach ($procurement->items as $item) {
                        $nama = $item->nama_barang;
                        $qty = $item->qty_pengajuan;
                        $satuan = $item->satuan;

                        // Ambil realisasi pertama (jika banyak)
                        $realisasi = $item->realisation;
                        // atau ->realisation jika kamu pakai hasOne

                        $harga = $realisasi
                            ? 'Rp ' . number_format($realisasi->harga_realisasi, 0, ',', '.')
                            : '<span class="italic text-gray-400">Belum</span>';

                        $hargaSatuan = $realisasi
                            ? 'Rp ' . number_format($realisasi->harga_realisasi / $qty, 0, ',', '.')
                            : '<span class="italic text-gray-400">Belum</span>';

                        $html .= <<<HTML
                                    <tr class="border-t border-gray-100 dark:border-gray-800">
                                        <td class="px-4 py-2">{$nama}</td>
                                        <td class="px-4 py-2">{$qty}</td>
                                        <td class="px-4 py-2">{$satuan}</td>
                                        <td class="px-4 py-2">{$hargaSatuan}</td>
                                        <td class="px-4 py-2">{$harga}</td>
                                    </tr>
                                HTML;
                    }

                    $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
        HTML;

                    return new \Illuminate\Support\HtmlString($html);
                })
                ->visible(fn(callable $get) => filled($get('procurement_id')))
                ->columnSpanFull(),

            Repeater::make('receiptItems')
                ->label('Daftar Aset yang Diterima')
                ->relationship()
                ->schema([
                    Select::make('template_id')
                        ->label('Template')
                        ->relationship('template', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state && $template = \App\Models\Template::find($state)) {
                                $set('custom_name', $template->name);
                                $set('code', $template->code);
                            }
                        })
                        ->createOptionForm([
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required(),
                            TextInput::make('name')
                                ->label('Nama Template')
                                ->required()
                                ->maxLength(50),
                            TextInput::make('code')
                                ->label('Kode Template')
                                ->required()
                                ->maxLength(50),
                        ]),

                    Select::make('lander_id')
                        ->label('Lander')
                        ->relationship('lander', 'name')
                        ->required(),

                    TextInput::make('custom_name')
                        ->label('Nama Aset')
                        ->required(),

                    TextInput::make('brand')
                        ->label('Merk')
                        ->nullable(),

                    TextInput::make('purchase_year')
                        ->label('Tahun Pembelian')
                        ->required()
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(date('Y'))
                        ->maxLength(4),

                    TextInput::make('tarif')
                        ->label('Harga')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('satuan')
                        ->label('Satuan')
                        ->nullable(),

                    TextInput::make('quantity')
                        ->label('Jumlah')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    TextInput::make('code')
                        ->label('Kode')
                        ->disabled()
                        ->nullable()
                        ->default(null),
                ])
                ->columnSpanFull()
                ->columns(4)
                ->createItemButtonLabel('Tambah Aset')
                ->minItems(1),
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Expand quantity menjadi multiple items
        $items = $data['receiptItems'] ?? [];
        $expandedItems = [];

        foreach ($items as $item) {
            $qty = $item['quantity'] ?? 1;
            for ($i = 0; $i < $qty; $i++) {
                $itemCopy = $item;
                unset($itemCopy['quantity']);
                $expandedItems[] = $itemCopy;
            }
        }

        $data['receiptItems'] = $expandedItems;

        return $data;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('receiptItems');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('procurement.procurement_id')
                    ->label('Pengadaan'),

                Tables\Columns\TextColumn::make('receipt_items_count')
                    ->label('Jumlah Item')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Qty')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->receiptItems->count()),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Harga')
                    ->getStateUsing(fn($record) => $record->receiptItems->sum(fn($item) => $item->tarif))
                    ->money('IDR'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsetReceipt::route('/'),
            'create' => Pages\CreateAsetReceipt::route('/create'),
            'edit' => Pages\EditAsetReceipt::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view aset receipt');
    }
}
