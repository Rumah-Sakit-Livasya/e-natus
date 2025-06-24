<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectRequestResource\Pages;
use App\Models\Aset;
use App\Models\Client;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Forms\Components\{DatePicker, Repeater, Select, Textarea, TextInput};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\{EditAction, DeleteAction, Action};
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

class ProjectRequestResource extends Resource
{
    protected static ?string $model = ProjectRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'List Project';
    protected static ?string $pluralModelLabel = 'List Project';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('client_id')
                    ->label('Klien')
                    ->options(Client::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $client = Client::with('region')->find($state);
                        if ($client) {
                            $set('pic', $client->pic);
                            $set('lokasi', $client->region?->name);
                        }
                    })
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('Nama Klien')->required(),
                        TextInput::make('pic')->label('PIC')->required(),
                        Select::make('region_id')
                            ->label('Wilayah')
                            ->options(\App\Models\Region::pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        TextInput::make('phone')->label('Nomor Telepon')->tel(),
                        TextInput::make('email')->label('Email')->email(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Client::create([
                            'name' => $data['name'],
                            'pic' => $data['pic'],
                            'region_id' => $data['region_id'] ?? null,
                            'phone' => $data['phone'] ?? null,
                            'email' => $data['email'] ?? null,
                        ])->id;
                    }),

                TextInput::make('name')->label('Nama Proyek')->required(),
                TextInput::make('pic')->label('PIC')->required(),
                Select::make('sdm_ids')
                    ->label('SDM')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(\App\Models\SDM::pluck('name', 'id')->toArray())
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('Nama SDM')->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\SDM::create([
                            'name' => $data['name'],
                        ])->id;
                    }),

                TextInput::make('jumlah')->label('Jumlah Peserta')->numeric()->required(),
                TextInput::make('lokasi')->label('Lokasi')->required(),

                DatePicker::make('start_period')
                    ->label('Periode Mulai')
                    ->required()
                    ->minDate(Carbon::today()),

                DatePicker::make('end_period')
                    ->label('Periode Selesai')
                    ->required()
                    ->minDate(Carbon::today()),

                Select::make('asset_ids')
                    ->label('Aset Terkait')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(fn() => Aset::where('status', 'available')
                        ->get()
                        ->mapWithKeys(function ($asset) {
                            $parts = explode('/', $asset->code);
                            $index = end($parts);
                            return [
                                $asset->id => "{$asset->custom_name} - {$asset->lander->code}$index"
                            ];
                        })
                        ->filter(fn($label) => !is_null($label)) // hindari label null
                        ->toArray())
                    ->createOptionForm(AsetResource::getAsetFormFields())
                    ->createOptionUsing(function (array $data) {
                        if (empty($data['code'])) {
                            unset($data['code']);
                        }

                        $aset = Aset::create($data);

                        // Tampilkan notifikasi sukses
                        Notification::make()
                            ->title('Aset berhasil ditambahkan')
                            ->body("Aset \"{$aset->custom_name}\" telah dibuat.")
                            ->success()
                            ->send();

                        return $aset->getKey();
                    }),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->disabled() // jika tidak boleh diubah
                    ->dehydrated() // pastikan tetap disimpan meskipun disabled
                    ->required(),

                Repeater::make('rencanaAnggaranBiaya')
                    ->label('Rencana Anggaran Biaya')
                    ->relationship() // otomatis isi project_request_id
                    ->schema([
                        TextInput::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->placeholder('Misalnya: Sewa AC Standing'),

                        TextInput::make('qty_aset')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->debounce(1000)
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $component) {
                                $qty = (int) $state ?? 0;
                                $harga = (int) $get('harga_sewa') ?? 0;

                                $livewire = $component->getLivewire();
                                $start = data_get($livewire->data, 'start_period');
                                $end = data_get($livewire->data, 'end_period');

                                $days = ProjectRequestResource::getDaysBetween($start, $end);
                                $set('total', $qty * $harga * $days);
                            }),

                        TextInput::make('harga_sewa')
                            ->label('Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->reactive()
                            ->debounce(1000)
                            ->afterStateUpdated(function ($state, callable $set, callable $get, $component) {
                                $qty = (int) $get('qty_aset') ?? 0;
                                $harga = (int) $state ?? 0;

                                $livewire = $component->getLivewire();
                                $start = data_get($livewire->data, 'start_period');
                                $end = data_get($livewire->data, 'end_period');

                                $days = ProjectRequestResource::getDaysBetween($start, $end);
                                $set('total', $qty * $harga * $days);
                            }),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->createItemButtonLabel('Tambah Item RAB'),

                TextInput::make('nilai_invoice')
                    ->label('Nilai Invoice')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->required(),

                Select::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partial paid' => 'Partial Paid',
                        'paid' => 'Paid',
                    ])
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->default('unpaid'),

                Textarea::make('keterangan')->label('Keterangan')->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama Proyek')->searchable()->sortable(),
            TextColumn::make('client.name')->label('Klien')->sortable(),
            TextColumn::make('pic')->label('PIC'),
            TextColumn::make('sdm_ids')
                ->label('SDM')
                ->formatStateUsing(function ($state) {
                    if (!$state) return '-';

                    // Jika string seperti "1, 2", ubah ke array
                    if (is_string($state)) {
                        $state = array_map('intval', explode(',', $state));
                    }

                    $names = \App\Models\SDM::whereIn('id', $state)->pluck('name')->toArray();
                    return implode(', ', $names);
                })
                ->sortable(),

            TextColumn::make('jumlah')->label('Jumlah Peserta')->numeric(),
            TextColumn::make('lokasi')->label('Lokasi'),
            TextColumn::make('user.name')->label('Dibuat oleh')->sortable(), // ← Tambahkan ini
            TextColumn::make('status')->label('Status')->badge()->color(fn(string $state): string => match ($state) {
                'pending' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
            })->sortable(),
        ])
            ->defaultSort('id', 'desc')
            ->actions([
                // EditAction::make()->icon('heroicon-o-pencil')->tooltip('Edit'),
                // DeleteAction::make()->icon('heroicon-o-trash')->tooltip('Hapus'),

                Action::make('viewAssets')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat Aset')
                    ->label('Aset')
                    ->modalHeading('Daftar Aset')
                    ->modalSubheading('Berikut adalah aset yang terkait dengan project ini.')
                    ->modalButton('Tutup')
                    ->action(fn() => null)
                    ->modalContent(fn($record) => new HtmlString(
                        Livewire::mount('project-asset-table', [
                            'assetIds' => $record->asset_ids ?? [],
                        ])
                    )),

                Action::make('viewRAB')
                    ->icon('heroicon-o-document-text')
                    ->tooltip('Lihat Rencana Anggaran Biaya')
                    ->label('RAB')
                    ->modalHeading('Rencana Anggaran Biaya')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn($record) => $record->rencanaAnggaranBiaya()->exists())
                    ->modalContent(function ($record) {
                        $rows = $record->rencanaAnggaranBiaya;
                        $total = $rows->sum('total');
                        $nilaiInvoice = $record->nilai_invoice;
                        $margin = $nilaiInvoice - $total;

                        return new \Illuminate\Support\HtmlString(
                            '<div class="flex justify-between items-center mb-4">
                                <a href="' . route('print-realisasi-rab', $record) . '" target="_blank"
                                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    🖨️ Cetak Realisasi
                                </a>

                                <a href="' . route('print-rab', $record) . '" target="_blank"
                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    🖨️ Cetak Halaman
                                </a>

                                <a href="' . route('project.realisasi-rab.create', $record) . '" target="_blank"
                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    ➕ Tambah Realisasi
                                </a>

                            </div>' .
                                view('components.project-request.view-rab-table', [
                                    'project' => $record,
                                    'rows' => $rows,
                                    'total' => $total,
                                    'nilaiInvoice' => $nilaiInvoice,
                                    'margin' => $margin,
                                ])->render()
                        );
                    }),

                Action::make('createRealisasiRab')
                    ->label('Tambah Realisasi RAB')
                    ->icon('heroicon-o-plus-circle')
                    ->tooltip('Tambah Realisasi RAB')
                    ->modalHeading('Tambah Realisasi RAB')
                    ->form(function ($record) {
                        return [
                            Select::make('rencana_anggaran_biaya_id')
                                ->label('Item RAB')
                                ->options(
                                    $record->rencanaAnggaranBiaya()
                                        ->pluck('description', 'id')
                                        ->toArray()
                                )
                                ->searchable()
                                ->required(),
                            TextInput::make('description')
                                ->label('Deskripsi')
                                ->required(),

                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->numeric(),

                            TextInput::make('harga')
                                ->label('Harga')
                                ->numeric(),

                            DatePicker::make('tanggal_realisasi')
                                ->label('Tanggal Realisasi'),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'done' => 'Done'
                                ])
                                ->default('draft')
                                ->required(),

                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->rows(2)
                                ->nullable(),
                        ];
                    })
                    ->action(function (array $data, $record) {
                        \App\Models\RealisationRabItem::create([
                            'project_request_id' => $record->id,
                            'rencana_anggaran_biaya_id' => $data['rencana_anggaran_biaya_id'],
                            'description' => $data['description'],
                            'qty' => $data['qty'],
                            'harga' => $data['harga'],
                            'total' => $data['qty'] * $data['harga'],
                            'tanggal_realisasi' => $data['tanggal_realisasi'],
                            'keterangan' => $data['keterangan'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Realisasi berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),

                Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->tooltip('Setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Permintaan Proyek')
                    ->modalSubheading('Apakah Anda yakin ingin menyetujui permintaan proyek ini?')
                    ->modalButton('Ya, Setujui')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        // Ubah status project request jadi approved
                        $record->update(['status' => 'approved']);

                        // Ambil dan update aset terkait
                        \App\Models\Aset::whereIn('id', $record->asset_ids ?? [])
                            ->update(['status' => 'unavailable']);
                    }),
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

    protected static function getDaysBetween($start, $end): int
    {
        if (!$start || !$end) return 1;
        try {
            $startDate = \Carbon\Carbon::parse($start);
            $endDate = \Carbon\Carbon::parse($end);
            return max($startDate->diffInDays($endDate) + 1, 1);
        } catch (\Exception $e) {
            return 1;
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectRequests::route('/'),
            'create' => Pages\CreateProjectRequest::route('/create'),
            'edit' => Pages\EditProjectRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view projects');
    }
}
