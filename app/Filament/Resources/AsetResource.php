<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsetResource\Pages;
use App\Models\Aset;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class AsetResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\AsetCluster::class;

    protected static ?string $model = Aset::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Aset';
    protected static ?string $modelLabel = 'Aset';
    protected static ?string $navigationGroup = 'Manajemen Aset';
    protected static ?string $pluralModelLabel = 'Data Aset';

    public static function form(Forms\Form $form): Forms\Form
    {
        // Cek apakah ini route edit atau create dari URI
        $isEdit = \Illuminate\Support\Str::contains(request()->path(), 'edit');

        return $form->schema(self::getAsetFormFields($isEdit));
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('template.name')
                    ->label('Template')
                    ->formatStateUsing(fn(?string $state): ?string => filled($state) ? Str::upper($state) : $state)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('template.category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lander.code')
                    ->label('Lander')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('custom_name')
                    ->label('Nama Aset')
                    ->formatStateUsing(fn(?string $state): ?string => filled($state) ? Str::upper($state) : $state)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Kode')
                    ->formatStateUsing(function (?string $state): ?string {
                        if (blank($state)) {
                            return $state;
                        }

                        $parts = explode('/', $state);

                        // Backward compatibility:
                        // old format: LANDER/CATEGORY/TEMPLATE/NNN -> LANDER/TEMPLATE/NNN
                        if (count($parts) >= 4) {
                            $state = "{$parts[0]}/{$parts[count($parts) - 2]}/{$parts[count($parts) - 1]}";
                        }

                        $normalizedParts = array_map(
                            fn($part) => strtoupper((string) preg_replace('/\s+/', '-', trim((string) $part))),
                            explode('/', $state)
                        );

                        return implode('/', $normalizedParts);
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand')
                    ->label('Merk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('purchase_year')
                    ->label('Tahun')->date('Y')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tarif')
                    ->label('Tarif')
                    ->searchable()
                    ->sortable()
                    ->money('IDR', true),

                TextColumn::make('harga_sewa')
                    ->label('Harga Sewa')
                    ->searchable()
                    ->sortable()
                    ->money('IDR', true),

                TextColumn::make('satuan')
                    ->label('Satuan')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'available'   => 'Tersedia',
                        'unavailable' => 'Tidak Tersedia',
                        default       => ucfirst($state),
                    })
                    ->color(fn(string $state) => match ($state) {
                        'available'   => 'success',
                        'unavailable' => 'danger',
                        default       => 'gray',
                    })
                    ->description(
                        fn($record) => $record->status === 'unavailable'
                            ? "Dipakai di: " . \App\Models\ProjectRequest::whereJsonContains('asset_ids', (int) $record->id)
                            ->pluck('name')
                            ->implode(', ')
                            : null
                    )
                    ->sortable(),

            ])
            ->headerActions([
                Tables\Actions\Action::make('print_all')
                    ->label('Cetak Aset')
                    ->icon('heroicon-o-printer')
                    ->url(route('print-assets'))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('print_barcodes')
                    ->label('Cetak Barcode')
                    ->icon('heroicon-o-viewfinder-circle')
                    ->url(route('print-asset-barcodes'))
                    ->openUrlInNewTab()
                    ->color('success'),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil')->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash')->tooltip('Hapus'),
                Tables\Actions\Action::make('view_barcode')
                    ->icon('heroicon-o-viewfinder-circle')
                    ->tooltip('Lihat Barcode')
                    ->label('Barcode')
                    ->modalHeading('Barcode Aset')
                    ->modalContent(fn($record) => view('filament.modals.asset-barcode', [
                        'asset' => $record,
                    ]))
                    ->color('primary'),
                Tables\Actions\Action::make('view_image')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat Gambar')
                    ->label('Lihat Gambar')
                    ->modalHeading('Preview Gambar Aset')
                    ->modalContent(fn($record) => view('filament.modals.preview-image', [
                        'imageUrl' => asset('storage/' . $record->image),
                    ]))
                    ->visible(fn($record) => filled($record->image)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListAsets::route('/'),
            'create' => Pages\CreateAset::route('/create'),
            'edit' => Pages\EditAset::route('/{record}/edit'),
        ];
    }

    // misalnya di App\Filament\Resources\AsetResource
    // app/Filament/Resources/AsetResource.php

    public static function getAsetFormFields(bool $isEdit = false): array
    {
        $fields = [
            Select::make('template_id')
                ->label('Template')
                ->options(\App\Models\Template::pluck('name', 'id')->toArray())
                ->searchable()
                ->required()
                ->native(false)
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nama Template')
                        ->required()
                        ->maxLength(50),

                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->maxLength(50),

                    Select::make('category_id')
                        ->label('Kategori')
                        ->options(fn() => \App\Models\Category::pluck('name', 'id')->toArray())
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Nama Kategori')
                                ->required()
                                ->maxLength(50),

                            TextInput::make('code')
                                ->label('Kode Kategori')
                                ->required()
                                ->maxLength(50),
                        ])
                ])

                ->createOptionUsing(function (array $data) {
                    return \App\Models\Template::create($data)->id;
                }),

            Select::make('lander_id')
                ->label('Lander')
                ->options(\App\Models\Lander::pluck('name', 'id'))
                ->native(false),

            TextInput::make('custom_name')
                ->label('Nama Aset')
                ->required()
                ->maxLength(50),

            TextInput::make('type')
                ->label('Tipe')
                ->nullable()
                ->maxLength(50),

            TextInput::make('serial_number')
                ->label('Serial Number')
                ->nullable()
                ->maxLength(50),

            // Hanya tampilkan field 'code' jika bukan edit
            // Jika $isEdit = true, field ini dihilangkan
        ];

        if (! $isEdit) {
            $fields[] = TextInput::make('code')
                ->label('Kode Aset')
                ->disabled() // jika tidak boleh diubah
                ->dehydrated() // pastikan tetap disimpan meskipun disabled
                ->maxLength(50);
        }

        $fields = array_merge($fields, [
            TextInput::make('condition')
                ->label('Kondisi')
                ->required()
                ->maxLength(50),

            TextInput::make('brand')
                ->label('Merk')
                ->nullable()
                ->maxLength(50),

            TextInput::make('purchase_year')
                ->label('Tahun Pembelian')
                ->required()
                ->numeric()
                ->minValue(1900)
                ->maxValue(date('Y'))
                ->maxLength(4),

            TextInput::make('tarif')
                ->label('Tarif')
                ->numeric()
                ->nullable(),

            TextInput::make('harga_sewa')
                ->label('Harga Sewa')
                ->numeric()
                ->nullable()
                ->helperText('Harga sewa untuk project request'),

            TextInput::make('satuan')
                ->label('Satuan')
                ->nullable(),

            FileUpload::make('image')
                ->label('Gambar')
                ->directory('aset-images')
                ->image()
                ->imageEditor()
                ->nullable(),

            Select::make('status')
                ->options([
                    'available' => 'Tersedia',
                    'unavailable' => 'Tidak Tersedia',
                ])
                ->required()
                ->default('available')
                ->disabled() // jika tidak boleh diubah
                ->dehydrated() // pastikan tetap disimpan meskipun disabled
                ->label('Status')
                ->native(false),
        ]);

        return $fields;
    }

    protected function afterCreate(): void
    {
        foreach ($this->data['asset_receipt_items'] ?? [] as $item) {
            Aset::create([
                'template_id'    => $item['template_id'],
                'custom_name'    => $item['custom_name'],
                'code'           => $item['code'] ?? null, // akan diisi otomatis kalau null
                'condition'      => $item['condition'],
                'brand'          => $item['brand'],
                'purchase_year'  => $item['purchase_year'],
                'tarif'          => $item['tarif'],
                'satuan'         => $item['satuan'],
                'status'         => 'available',
                'lander_id'      => $item['lander_id'],
                'receipt_id'     => $this->record->id,
            ]);
        }
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view asets');
    }
}
