<?php

namespace App\Filament\Resources;

// ... (semua use statement tetap sama)
use App\Filament\Resources\ProjectRequestResource\Pages;
use App\Filament\Resources\ProjectRequestResource\Pages\CompareRab;
use App\Filament\Pages\ProjectFinanceComparison;
use App\Models\Aset;
use App\Models\Client;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Forms\Components\{DatePicker, Repeater, Select, Textarea, TextInput};
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Pages\Route;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\{Action};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

class ProjectRequestResource extends Resource
{
    // ... (properti model dan navigasi tidak berubah)
    protected static ?string $model = ProjectRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $navigationLabel = 'List Project';
    protected static ?string $pluralModelLabel = 'List Project';


    public static function form(Form $form): Form
    {
        // ... (seluruh fungsi form() tidak perlu diubah, karena sudah benar)
        return $form
            ->schema([
                Select::make('client_id')
                    ->label('Klien')
                    ->options(Client::pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $client = Client::with('region')
                            ->find($state);
                        if ($client) {
                            $set('pic', $client->pic);
                            $set('lokasi', $client->region?->name);
                        }
                    })
                    ->required()
                    ->createOptionForm([TextInput::make('name')
                        ->label('Nama Klien')
                        ->required(), TextInput::make('pic')
                        ->label('PIC')
                        ->required(), Select::make('region_id')
                        ->label('Wilayah')
                        ->options(\App\Models\Region::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(), TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel(), TextInput::make('email')
                        ->label('Email')
                        ->email(),])
                    ->createOptionUsing(function (array $data) {
                        return Client::create(['name' => $data['name'], 'pic' => $data['pic'], 'region_id' => $data['region_id'] ?? null, 'phone' => $data['phone'] ?? null, 'email' => $data['email'] ?? null,])
                            ->id;
                    }),

                TextInput::make('name')
                    ->label('Nama Proyek')
                    ->required(),

                Select::make('employee_ids')
                    ->label('PIC')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(\App\Models\Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name', 'employees.id')
                        ->toArray())
                    ->required()
                    ->createOptionForm([TextInput::make('name')
                        ->label('Nama Pegawai')
                        ->required(),])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\SDM::create(['name' => $data['name'],])
                            ->id;
                    }),

                Select::make('sdm_ids')
                    ->label('SDM')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(\App\Models\SDM::pluck('name', 'id')
                        ->toArray())
                    ->required()
                    ->createOptionForm([TextInput::make('name')
                        ->label('Nama SDM')
                        ->required(),])
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\SDM::create(['name' => $data['name'],])
                            ->id;
                    }),

                TextInput::make('jumlah')
                    ->label('Jumlah Peserta')
                    ->numeric()
                    ->required(),

                TextInput::make('lokasi')
                    ->label('Lokasi')
                    ->required(),

                DatePicker::make('start_period')
                    ->label('Periode Mulai')
                    ->required()
                    ->minDate(Carbon::today())
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateAllTotalsInRepeater($get, $set);
                    }),

                DatePicker::make('end_period')
                    ->label('Periode Selesai')
                    ->required()
                    ->minDate(Carbon::today())
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateAllTotalsInRepeater($get, $set);
                    }),

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
                            return [$asset->id => "{$asset->custom_name} - {$asset->lander->code}$index"];
                        })
                        ->filter(fn($label) => !is_null($label))
                        ->toArray())
                    ->createOptionForm(AsetResource::getAsetFormFields())
                    ->createOptionUsing(function (array $data) {
                        if (empty($data['code'])) {
                            unset($data['code']);
                        }
                        $aset = Aset::create($data);
                        Notification::make()
                            ->title('Aset berhasil ditambahkan')
                            ->body("Aset \"{$aset->custom_name}\" telah dibuat.")
                            ->success()
                            ->send();
                        return $aset->getKey();
                    }),

                Select::make('status')
                    ->label('Status')
                    ->options(['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'done' => 'Selesai',])
                    ->default('pending')
                    ->disabled()
                    ->dehydrated()
                    ->required(),

                Repeater::make('rencanaAnggaranBiaya')
                    ->label('Rencana Anggaran Biaya')
                    ->relationship()
                    ->schema([
                        TextInput::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->placeholder('Misalnya: Sewa AC Standing'),

                        TextInput::make('qty_aset')
                            ->label('Jumlah')
                            ->numeric()
                            ->required()
                            ->live()
                            ->debounce(500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set)),

                        TextInput::make('harga_sewa')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->live()
                            ->debounce(500)
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set)),

                        TextInput::make('total')
                            ->label('Total')
                            ->prefix('Rp')
                            ->numeric(0, ',', '.')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->createItemButtonLabel('Tambah Item RAB'),

                TextInput::make('nilai_invoice')
                    ->label('Nilai Invoice')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null),

                DatePicker::make('due_date')
                    ->label('Jatuh Tempo')
                    ->required(),

                Select::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options(['unpaid' => 'Unpaid', 'partial paid' => 'Partial Paid', 'paid' => 'Paid',])
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->default('unpaid'),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
            ]);
    }

    // ... (fungsi table() tidak berubah)
    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')
            ->label('Nama Proyek')
            ->searchable()
            ->sortable(), TextColumn::make('client.name')
            ->label('Klien')
            ->sortable(), TextColumn::make('pic')
            ->label('PIC'), TextColumn::make('employee_ids')
            ->label('Employee')
            ->formatStateUsing(function ($state) {
                if (empty($state)) return '-';
                if (!is_array($state)) {
                    $decoded = json_decode($state, true);
                    $state = is_array($decoded) ? $decoded : array_map('intval', explode(',', (string)$state));
                }
                $state = array_filter($state);
                if (empty($state)) return '-';
                $names = \App\Models\Employee::whereIn('employees.id', $state)
                    ->join('users', 'employees.user_id', '=', 'users.id')
                    ->pluck('users.name')
                    ->toArray();
                return implode(', ', $names);
            }), TextColumn::make('sdm_ids')
            ->label('SDM')
            ->formatStateUsing(function ($state) {
                if (!$state) return '-';
                if (is_string($state)) {
                    $state = array_map('intval', explode(',', $state));
                }
                $names = \App\Models\SDM::whereIn('id', $state)
                    ->pluck('name')
                    ->toArray();
                return implode(', ', $names);
            }), TextColumn::make('jumlah')
            ->label('Jumlah Peserta')
            ->numeric(), TextColumn::make('lokasi')
            ->label('Lokasi'), TextColumn::make('user.name')
            ->label('Dibuat oleh')
            ->sortable(), TextColumn::make('status')
            ->label('Status')
            ->badge()
            ->color(fn(string $state): string => match ($state) {
                'pending' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
                'done' => 'success',
            })
            ->sortable(),])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Action::make('createRealisasiRab')
                    ->label('Tambah Realisasi RAB')
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading('Tambah Realisasi RAB')
                    ->hidden()
                    ->form(function ($record) {
                        return [Select::make('rencana_anggaran_biaya_id')
                            ->label('Item RAB')
                            ->options($record->rencanaAnggaranBiaya()
                                ->pluck('description', 'id')
                                ->toArray())
                            ->searchable()
                            ->required(), TextInput::make('description')
                            ->label('Deskripsi')
                            ->required(), TextInput::make('qty')
                            ->label('Jumlah')
                            ->numeric(), TextInput::make('harga')
                            ->label('Harga')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null), DatePicker::make('tanggal_realisasi')
                            ->label('Tanggal Realisasi'), Select::make('status')
                            ->label('Status')
                            ->options(['draft' => 'Draft', 'approved' => 'Approved', 'rejected' => 'Rejected', 'done' => 'Done'])
                            ->default('draft')
                            ->required(), Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->nullable(),];
                    })
                    ->action(function (array $data, $record) {
                        \App\Models\RealisationRabItem::create(['project_request_id' => $record->id, 'rencana_anggaran_biaya_id' => $data['rencana_anggaran_biaya_id'], 'description' => $data['description'], 'qty' => $data['qty'], 'harga' => $data['harga'], 'total' => $data['qty'] * $data['harga'], 'tanggal_realisasi' => $data['tanggal_realisasi'], 'keterangan' => $data['keterangan'] ?? null,]);
                        Notification::make()
                            ->title('Realisasi berhasil ditambahkan')
                            ->success()
                            ->send();
                    }),

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
                    ->modalWidth('4xl')
                    ->visible(fn($record) => $record->rencanaAnggaranBiaya()
                        ->exists())
                    ->modalContent(function ($record) {
                        return view('filament.tables.actions.view-rab-modal-content', ['record' => $record]);
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
                        $record->update(['status' => 'approved']);
                        \App\Models\Aset::whereIn('id', $record->asset_ids ?? [])
                            ->update(['status' => 'unavailable']);
                    }),

                Action::make('createClosingRab')
                    ->label('RAB Closing')
                    ->icon('heroicon-o-document-check')
                    ->color('warning')
                    ->action(function (ProjectRequest $record, $livewire) {
                        // Cek jika draft sudah ada
                        if ($record->rabClosing()->exists()) {
                            Notification::make()
                                ->title('Informasi')
                                ->body('Draft RAB Closing sudah ada. Silakan edit dari sana.')
                                ->info()
                                ->send();
                            return redirect()
                                ->to(RabClosingResource::getUrl('edit', ['record' => $record->rabClosing->id]));
                        }

                        try {
                            DB::beginTransaction();

                            // --- PERUBAHAN DIMULAI DI SINI ---

                            // 1. Hitung total anggaran dari RAB awal (sudah benar)
                            $totalAnggaranAwal = $record->rencanaAnggaranBiaya()->sum('total');

                            // 2. Hitung total realisasi dari semua item realisasi yang terkait
                            $totalRealisasiAwal = $record->realisationRabItems()->sum('total');

                            // 3. Hitung selisih awal
                            $selisihAwal = $totalAnggaranAwal - $totalRealisasiAwal;
                            // dd($totalAnggaranAwal . " " . $totalRealisasiAwal . " " . $selisihAwal);

                            // 4. Buat record RabClosing dengan data yang sudah dihitung
                            $rabClosing = $record->rabClosing()->create([
                                'closing_date'    => now(),
                                'status'          => 'draft',
                                'total_anggaran'  => $totalAnggaranAwal,
                                'total_realisasi' => $totalRealisasiAwal, // Menggunakan total realisasi yang dihitung
                                'selisih'         => $selisihAwal,         // Menggunakan selisih yang dihitung
                            ]);

                            // --- AKHIR PERUBAHAN ---

                            // Salin item dari RAB awal ke item RAB Closing (logika ini tetap sama)
                            foreach ($record->rencanaAnggaranBiaya as $itemAwal) {
                                // Untuk bagian ini, kita hanya menyalin data ANGGARAN.
                                // Data REALISASI akan diisi/diedit di form RAB Closing nanti.
                                $rabClosing->items()->create([
                                    'description'    => $itemAwal->description,
                                    'qty'            => $itemAwal->qty_aset,
                                    'harga_satuan'   => $itemAwal->harga_sewa,
                                    'total_anggaran' => $itemAwal->total,
                                    // Anda bisa menambahkan kolom realisasi di sini jika perlu,
                                    // atau membiarkannya kosong untuk diisi nanti.
                                    // 'harga_realisasi' => 0,
                                    // 'total_realisasi' => 0,
                                ]);
                            }

                            DB::commit();
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Draft RAB Closing berhasil dibuat dengan data realisasi terkini.')
                                ->success()
                                ->send();
                            return redirect()->to(RabClosingResource::getUrl('edit', ['record' => $rabClosing->id]));
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()->title('Terjadi Kesalahan')->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->visible(fn(ProjectRequest $record): bool => $record->status === 'approved'),

                Tables\Actions\Action::make('compare')
                    ->label('Bandingkan RAB')
                    ->icon('heroicon-o-scale')
                    ->color('info')
                    // Arahkan ke URL halaman kustom kita dengan query parameter
                    ->url(fn(ProjectRequest $record): string => ProjectFinanceComparison::getUrl(['project' => $record->id])),

                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make(),]);
    }

    // --- PERBAIKAN DI SINI ---
    protected static function cleanMoneyValue(?string $value): int|float
    {
        if ($value === null || $value === '') {
            return 0;
        }
        // Hapus semua karakter yang BUKAN angka.
        // Ini akan mengubah "10,000,000" menjadi "10000000"
        return (float) preg_replace('/[^\d]/', '', $value);
    }

    // Fungsi updateRowTotal sekarang akan bekerja dengan benar karena cleanMoneyValue sudah diperbaiki
    protected static function updateRowTotal(Get $get, Set $set): void
    {
        $qty = (int) ($get('qty_aset') ?? 0);
        $harga = self::cleanMoneyValue($get('harga_sewa'));
        // $start = $get('../../start_period');
        // $end = $get('../../end_period');
        // $days = self::getDaysBetween($start, $end);

        // $set('total', $qty * $harga * $days);
        $set('total', $qty * $harga);
    }

    // Fungsi updateAllTotalsInRepeater sekarang juga akan bekerja dengan benar
    protected static function updateAllTotalsInRepeater(Get $get, Set $set): void
    {
        $items = $get('rencanaAnggaranBiaya');
        $start = $get('start_period');
        $end = $get('end_period');
        $days = self::getDaysBetween($start, $end);

        $updatedItems = [];
        if (is_array($items)) {
            foreach ($items as $key => $item) {
                $qty = (int) ($item['qty_aset'] ?? 0);
                $harga = self::cleanMoneyValue($item['harga_sewa']);

                $item['total'] = $qty * $harga * $days;
                $updatedItems[$key] = $item;
            }
        }

        $set('rencanaAnggaranBiaya', $updatedItems);
    }

    // ... sisa fungsi (getRelations, getDaysBetween, getPages, canViewAny) tidak berubah
    public static function getRelations(): array
    {
        return [];
    }
    protected static function getDaysBetween($start, $end): int
    {
        if (!$start || !$end) return 1;
        try {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            return max($startDate->diffInDays($endDate) + 1, 1);
        } catch (\Exception $e) {
            return 1;
        }
    }
    // Di dalam ProjectRequestResource.php

    // Di dalam ProjectRequestResource.php

    // Di dalam ProjectRequestResource.php

    // Di dalam ProjectRequestResource.php

    public static function getPages(): array
    {
        // HANYA daftarkan halaman CRUD standar
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
            return true;
        }
        return auth()->user()->can('view projects');
    }
}
