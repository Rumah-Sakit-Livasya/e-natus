<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectRequestResource\Pages;
use App\Filament\Resources\ProjectRequestResource\RelationManagers;
use App\Models\GeneralSetting;
use App\Models\Aset;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle; // <-- Tambahkan ini untuk toggle PPN
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Livewire;

class ProjectRequestResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = \App\Filament\Clusters\ProjectCluster::class;

    protected static ?string $model = ProjectRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Project';

    protected static ?string $navigationLabel = 'List Project';

    protected static ?string $pluralModelLabel = 'List Project';

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                    Select::make('region_id')->label('Wilayah')->options(\App\Models\Region::pluck('name', 'id'))->searchable()->nullable(),
                    TextInput::make('phone')->label('Nomor Telepon')->tel(),
                    TextInput::make('email')->label('Email')->email(),
                ])
                ->createOptionUsing(fn(array $data) => Client::create([
                    'name' => $data['name'],
                    'pic' => $data['pic'],
                    'region_id' => $data['region_id'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                ])->id),

            TextInput::make('name')->label('Nama Proyek')->required(),

            Select::make('employee_ids')
                ->label('PIC')
                ->searchable()
                ->preload()
                ->options(
                    \App\Models\Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name', 'employees.id')
                        ->toArray()
                )
                ->afterStateHydrated(function (Select $component, $state): void {
                    // Backward compatibility: old records may store PIC as array/json.
                    if (is_array($state)) {
                        $component->state($state[0] ?? null);
                    }
                })
                ->dehydrateStateUsing(fn($state) => is_array($state) ? ($state[0] ?? null) : $state)
                ->required()
                ->createOptionForm([
                    TextInput::make('name')->label('Nama Pegawai')->required(),
                ])
                ->createOptionUsing(fn(array $data) => \App\Models\SDM::create([
                    'name' => $data['name'],
                ])->id),

            Select::make('sdm_ids')
                ->label('Pegawai Ditugaskan')
                ->helperText('Pilih satu atau lebih pegawai yang ditugaskan di project ini.')
                ->multiple()
                ->searchable()
                ->preload()
                ->live()
                ->options(
                    Employee::join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name', 'employees.id')
                        ->toArray()
                )
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    self::syncAssignedStaffFeeItems($get, $set, $state);
                })
                ->required(),

            TextInput::make('jumlah')->label('Jumlah Peserta')->numeric()->required(),
            TextInput::make('lokasi')->label('Lokasi')->required(),
            DatePicker::make('start_period')->label('Periode Mulai')->required(),
            DatePicker::make('end_period')->label('Periode Selesai')->required(),

            Select::make('asset_ids')
                ->label('Aset Terkait')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(fn() => Aset::where('status', 'available')->get()->mapWithKeys(function ($asset) {
                    $assetName = Str::upper((string) $asset->custom_name);
                    $assetCode = self::normalizeAssetCode((string) $asset->code);

                    return [$asset->id => "{$assetName} - {$assetCode}"];
                })->filter(fn($label) => ! is_null($label))->toArray())
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    $selectedAssets = Aset::whereIn('id', $state)->get();
                    $currentItems = $get('rabOperasionalItems') ?? [];

                    // Keep existing items that are NOT from assets (optional, depending on requirement)
                    // Or simply append new assets if they aren't already there.

                    foreach ($selectedAssets as $asset) {
                        $assetName = Str::upper((string) $asset->custom_name);
                        $assetCode = self::normalizeAssetCode((string) $asset->code);
                        $description = "SEWA INTERNAL {$assetName} - {$assetCode}";

                        // Check if this asset is already in the list to avoid duplicates
                        $exists = collect($currentItems)->contains(function ($item) use ($description) {
                            return isset($item['description']) && $item['description'] === $description;
                        });

                        if (! $exists) {
                            $currentItems[] = [
                                'description' => $description,
                                'qty_aset' => 1,
                                'harga_sewa' => number_format((float) ($asset->harga_sewa ?? 0), 0, '.', ','), // Use harga_sewa instead of tarif
                                'total' => number_format((float) ($asset->harga_sewa ?? 0), 0, '.', ','),
                                'is_internal_rental' => true, // Flag to identify as internal rental
                            ];
                        }
                    }

                    $set('rabOperasionalItems', $currentItems);
                })
                ->createOptionForm(AsetResource::getAsetFormFields())
                ->createOptionUsing(function (array $data) {
                    if (empty($data['code'])) {
                        unset($data['code']);
                    }
                    $aset = Aset::create($data);
                    Notification::make()
                        ->title('Aset berhasil ditambahkan')
                        ->body('Aset "' . Str::upper((string) $aset->custom_name) . '" telah dibuat.')
                        ->success()
                        ->send();

                    return $aset->getKey();
                }),

            Select::make('vendor_rental_ids')
                ->label('Sewa Alat Vendor (Alternatif)')
                ->multiple()
                ->searchable()
                ->preload()
                ->options(\App\Models\VendorRental::pluck('name', 'id')->toArray())
                ->live()
                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                    $selectedRentals = \App\Models\VendorRental::whereIn('id', $state)->get();
                    $currentItems = $get('rabOperasionalItems') ?? [];

                    foreach ($selectedRentals as $rental) {
                        $description = "SEWA {$rental->name} ({$rental->unit})";

                        // Check if this rental is already in the list
                        $exists = collect($currentItems)->contains(function ($item) use ($description) {
                            return isset($item['description']) && $item['description'] === $description;
                        });

                        if (! $exists) {
                            $currentItems[] = [
                                'description' => $description,
                                'qty_aset' => $rental->qty,
                                'harga_sewa' => number_format((float) $rental->price, 0, '.', ','),
                                'total' => number_format((float) ($rental->qty * $rental->price), 0, '.', ','),
                                'is_vendor_rental' => true,
                            ];
                        }
                    }

                    $set('rabOperasionalItems', $currentItems);
                }),


            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'done' => 'Selesai',
                ])
                ->default('pending')
                ->disabled()
                ->dehydrated()
                ->required(),

            // =================== APPROVAL STATUS DISPLAY ===================
            Section::make('Status Persetujuan')
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('approval_level_1_display')
                        ->label('Persetujuan Level 1')
                        ->content(function ($record) {
                            if (!$record) {
                                return 'Belum ada data';
                            }

                            $status = match ($record->approval_level_1_status) {
                                'pending' => '⏳ Menunggu Persetujuan',
                                'approved' => '✅ Disetujui',
                                'rejected' => '❌ Ditolak',
                                default => '-'
                            };

                            $approver = $record->approvalLevel1By?->name ?? '-';
                            $time = $record->approval_level_1_at?->format('d M Y, H:i') ?? '-';
                            $notes = $record->approval_level_1_notes ?? '-';

                            return new HtmlString("
                                <div class='space-y-2'>
                                    <div><strong>Status:</strong> {$status}</div>
                                    <div><strong>Disetujui oleh:</strong> {$approver}</div>
                                    <div><strong>Waktu:</strong> {$time}</div>
                                    <div><strong>Catatan:</strong> {$notes}</div>
                                </div>
                            ");
                        })
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Placeholder::make('approval_level_2_display')
                        ->label('Persetujuan Level 2')
                        ->content(function ($record) {
                            if (!$record) {
                                return 'Belum ada data';
                            }

                            $status = match ($record->approval_level_2_status) {
                                'pending' => '⏳ Menunggu Persetujuan',
                                'approved' => '✅ Disetujui',
                                'rejected' => '❌ Ditolak',
                                default => '-'
                            };

                            $approver = $record->approvalLevel2By?->name ?? '-';
                            $time = $record->approval_level_2_at?->format('d M Y, H:i') ?? '-';
                            $notes = $record->approval_level_2_notes ?? '-';

                            return new HtmlString("
                                <div class='space-y-2'>
                                    <div><strong>Status:</strong> {$status}</div>
                                    <div><strong>Disetujui oleh:</strong> {$approver}</div>
                                    <div><strong>Waktu:</strong> {$time}</div>
                                    <div><strong>Catatan:</strong> {$notes}</div>
                                </div>
                            ");
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(false)
                ->visible(fn($record) => $record !== null),
            // =================== END APPROVAL STATUS DISPLAY ===================

            Section::make('Rencana Biaya Operasional')
                ->collapsible()
                ->schema([
                    Repeater::make('rabOperasionalItems')
                        ->relationship()
                        ->label(false)
                        ->defaultItems(0)
                        ->schema([
                            TextInput::make('description')
                                ->label('Deskripsi')
                                ->required()
                                ->placeholder('Misalnya: Sewa Mobil Box'),

                            TextInput::make('qty_aset')
                                ->label('Jumlah')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set)),

                            TextInput::make('harga_sewa')
                                ->label('Price')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live(onBlur: true)
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set))
                                ->disabled(fn(Get $get) => (($get('is_vendor_rental') ?? false) || ($get('is_internal_rental') ?? false)) && ! auth()->user()->hasAnyRole(['super-admin', 'owner']))
                                ->dehydrated()
                                ->suffixAction(
                                    \Filament\Forms\Components\Actions\Action::make('requestPriceChange')
                                        ->label('Request Change')
                                        ->icon('heroicon-o-pencil')
                                        ->visible(function (Get $get, $state) {
                                            // Show if item is vendor rental OR internal rental and user doesn't have permission
                                            return (($get('is_vendor_rental') ?? false) || ($get('is_internal_rental') ?? false)) && ! auth()->user()->hasAnyRole(['super-admin', 'owner']);
                                        })
                                        ->form([
                                            \Filament\Forms\Components\TextInput::make('requested_price')
                                                ->label('Requested Price')
                                                ->numeric()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',')
                                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                                                ->required()
                                                ->prefix('Rp'),
                                            \Filament\Forms\Components\Textarea::make('reason')
                                                ->label('Reason for Change')
                                                ->required()
                                                ->rows(3),
                                        ])
                                        ->action(function (array $data, Get $get, $state, $livewire) {
                                            // Create price change request
                                            $rabItem = \App\Models\RabOperasionalItem::find($get('../../id'));

                                            if ($rabItem) {
                                                $request = \App\Models\PriceChangeRequest::create([
                                                    'rab_operasional_item_id' => $rabItem->id,
                                                    'requested_by' => auth()->id(),
                                                    'current_price' => $rabItem->harga_sewa,
                                                    'requested_price' => $data['requested_price'],
                                                    'reason' => $data['reason'],
                                                ]);

                                                // Notify users with permission - menggunakan role super-admin dan owner
                                                $approvers = \App\Models\User::role(['super-admin', 'owner'])->get();
                                                foreach ($approvers as $approver) {
                                                    $approver->notify(new \App\Notifications\PriceChangeRequestNotification($request));
                                                }

                                                \Filament\Notifications\Notification::make()
                                                    ->title('Price change request submitted')
                                                    ->success()
                                                    ->send();
                                            }
                                        })
                                ),

                            \Filament\Forms\Components\Hidden::make('is_vendor_rental')
                                ->default(false)
                                ->dehydrated(),

                            \Filament\Forms\Components\Hidden::make('is_internal_rental')
                                ->default(false)
                                ->dehydrated(),

                            TextInput::make('total')
                                ->label('Total')
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                                ->required(),
                        ])
                        ->columns(4)
                        ->createItemButtonLabel('Tambah Item Operasional'),
                ]),

            Section::make('Petugas Ditugaskan')
                ->description('Input fee petugas internal dari "Pegawai Ditugaskan", dan bisa tambah manual untuk pegawai eksternal.')
                ->collapsible()
                ->schema([
                    Repeater::make('rabFeeItems')
                        ->relationship()
                        ->label(false)
                        ->defaultItems(0)
                        ->reorderable(false)
                        ->createItemButtonLabel('Tambah Pegawai External')
                        ->schema([
                            \Filament\Forms\Components\Hidden::make('assigned_employee_id')
                                ->dehydrated(false),

                            TextInput::make('description')
                                ->label('Nama Petugas')
                                ->required()
                                ->placeholder('Misalnya: Dr. A (External)'),

                            TextInput::make('qty_aset')
                                ->label('Jumlah')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set)),

                            TextInput::make('harga_sewa')
                                ->label('Fee')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->live(onBlur: true)
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                                ->afterStateUpdated(fn(Get $get, Set $set) => self::updateRowTotal($get, $set)),

                            TextInput::make('total')
                                ->label('Total')
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->mask(RawJs::make('$money($input)'))
                                ->stripCharacters(',')
                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null)
                                ->required(),
                        ])
                        ->columns(4),
                ]),

            Repeater::make('bmhp')
                ->relationship('projectBmhp') // gunakan relasi yang benar
                ->label('BMHP yang digunakan')
                ->defaultItems(0)
                ->schema([
                    Select::make('bmhp_id')
                        ->label('BMHP')
                        ->searchable()
                        ->options(\App\Models\Bmhp::pluck('name', 'id')->toArray())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $bmhp = \App\Models\Bmhp::find($state);
                            if ($bmhp) {
                                $set('pcs_per_unit_snapshot', $bmhp->pcs_per_unit);
                                $set('satuan', $bmhp->satuan);
                            }
                        })
                        ->helperText(function (Get $get) {
                            $bmhpId = $get('bmhp_id');
                            if (! $bmhpId) {
                                return 'Pilih BMHP untuk melihat stok...';
                            }
                            $bmhp = \App\Models\Bmhp::with('projectBmhp')->find($bmhpId);
                            if (! $bmhp) {
                                return 'Data tidak ditemukan';
                            }

                            $stokSisa = (int) ($bmhp->stok_sisa ?? 0);
                            $pcsPerUnit = (int) ($bmhp->pcs_per_unit ?? 1);

                            if ($pcsPerUnit <= 1) {
                                return new HtmlString("Stok Tersedia: <strong class='text-primary-600'>{$stokSisa}</strong> {$bmhp->satuan}");
                            }

                            $units = floor($stokSisa / $pcsPerUnit);
                            $pcs = $stokSisa % $pcsPerUnit;

                            $displayText = "Stok Tersedia: <strong class='text-primary-600'>{$units}</strong> {$bmhp->satuan}";
                            if ($pcs > 0) {
                                $displayText .= " + <strong class='text-primary-600'>{$pcs}</strong> pcs";
                            }
                            $displayText .= " (Total: {$stokSisa} pcs)";

                            return new HtmlString($displayText);
                        }),

                    Select::make('purchase_type')
                        ->label('Satuan')
                        ->options(function (Get $get) {
                            $satuan = $get('satuan') ?: 'Unit';

                            return [
                                'unit' => "$satuan (unit)",
                                'pcs' => 'Pcs',
                            ];
                        })
                        ->default('pcs')
                        ->required()
                        ->live()
                        ->native(false)
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateBmhpRowTotal($get, $set)),

                    TextInput::make('qty')
                        ->label('Jumlah')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateBmhpRowTotal($get, $set)),

                    TextInput::make('harga_satuan')
                        ->label('Price')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->live(onBlur: true)
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->dehydrateStateUsing(fn(?string $state) => $state ? preg_replace('/[^\d]/', '', $state) : null)
                        ->afterStateUpdated(fn(Get $get, Set $set) => self::updateBmhpRowTotal($get, $set)),

                    TextInput::make('total')
                        ->label('Total')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated()
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->dehydrateStateUsing(fn(?string $state) => $state ? preg_replace('/[^\d]/', '', $state) : null),

                    TextInput::make('pcs_per_unit_snapshot')
                        ->label('Isi per Unit (Pcs)')
                        ->numeric()
                        ->readOnly()
                        ->default(1)
                        ->dehydrated(true)
                        ->helperText('Diambil otomatis dari master.'),
                    \Filament\Forms\Components\Hidden::make('satuan')
                        ->dehydrated(true)
                        ->dehydrateStateUsing(fn($state) => $state ?? ''),
                    \Filament\Forms\Components\Hidden::make('jumlah_rencana')
                        ->default(0)
                        ->dehydrated(true)
                        ->dehydrateStateUsing(fn($state) => (int) ($state ?? 0)), // total_pcs
                ])
                ->columns(5)
                ->columnSpanFull()
                ->createItemButtonLabel('Tambah BMHP'),

            TextInput::make('nilai_invoice')
                ->label('Nilai Invoice')
                ->prefix('Rp')
                ->required()
                ->mask(RawJs::make('$money($input)'))
                ->stripCharacters(',')
                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? preg_replace('/[^\d]/', '', $state) : null),

            // =================== PERUBAHAN DIMULAI DI SINI ===================
            Toggle::make('with_ppn')
                ->label('Sertakan PPN?')
                ->reactive()
                ->default(false),

            TextInput::make('ppn_percentage')
                ->label('Persentase PPN (%)')
                ->numeric()
                ->default(11)
                ->required()
                ->visible(fn(Get $get) => $get('with_ppn')),
            // =================== PERUBAHAN SELESAI DI SINI ===================

            DatePicker::make('due_date')->label('Jatuh Tempo')->required(),

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
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Proyek')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Klien')->sortable(),
                TextColumn::make('pic')->label('PIC'),
                TextColumn::make('employee_ids')->label('PIC')->formatStateUsing(function ($state) {
                    if (empty($state)) {
                        return '-';
                    }
                    if (! is_array($state)) {
                        $decoded = json_decode($state, true);
                        $state = is_array($decoded) ? $decoded : array_map('intval', explode(',', (string) $state));
                    }
                    $state = array_filter($state);
                    if (empty($state)) {
                        return '-';
                    }
                    $names = \App\Models\Employee::whereIn('employees.id', $state)
                        ->join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name')
                        ->toArray();

                    return implode(', ', $names);
                }),
                TextColumn::make('sdm_ids')->label('Pegawai Ditugaskan')->formatStateUsing(function ($state) {
                    if (empty($state)) {
                        return '-';
                    }
                    if (! is_array($state)) {
                        $decoded = json_decode($state, true);
                        $state = is_array($decoded) ? $decoded : array_map('intval', explode(',', (string) $state));
                    }
                    $state = array_filter($state);
                    if (empty($state)) {
                        return '-';
                    }
                    $names = \App\Models\Employee::whereIn('employees.id', $state)
                        ->join('users', 'employees.user_id', '=', 'users.id')
                        ->pluck('users.name')
                        ->toArray();

                    return implode(', ', $names);
                }),
                TextColumn::make('jumlah')->label('Jumlah Peserta')->numeric(),
                TextColumn::make('lokasi')->label('Lokasi'),
                TextColumn::make('user.name')->label('Dibuat oleh')->sortable(),
                TextColumn::make('status')->label('Status')->badge()->color(fn(string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'done' => 'success',
                })->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([])
            ->actions([
                Action::make('addParticipant')
                    ->label('Tambah Peserta')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->url(
                        fn(ProjectRequest $record): string => \App\Filament\Resources\ParticipantResource::getUrl('create', ['project_request_id' => $record->id])
                    )
                    ->visible(function (ProjectRequest $record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }
                        // Safely check isSuperAdmin, handling possible bool return
                        if (
                            method_exists($user, 'isSuperAdmin')
                        ) {
                            $isSuperAdmin = $user->isSuperAdmin();
                            // If isSuperAdmin is a boolean
                            if (is_bool($isSuperAdmin)) {
                                if ($isSuperAdmin) {
                                    return $record->status === 'approved';
                                }
                                // If isSuperAdmin returns a relation/query builder
                            } elseif (is_object($isSuperAdmin) && method_exists($isSuperAdmin, 'exists')) {
                                if ($isSuperAdmin->exists()) {
                                    return $record->status === 'approved';
                                }
                            }
                        }

                        return $record->status === 'approved' && $user->can('view hasil mcu');
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
                            'projectRequestId' => $record->id,
                        ])
                    )),

                Action::make('viewAssignedEmployees')
                    ->icon('heroicon-o-users')
                    ->tooltip('Lihat Pegawai Ditugaskan')
                    ->label('Pegawai')
                    ->color('info')
                    ->modalHeading('Daftar Pegawai Ditugaskan')
                    ->modalSubheading('Berikut adalah pegawai yang ditugaskan pada project ini.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('4xl')
                    ->action(fn() => null)
                    ->modalContent(function (ProjectRequest $record): View {
                        $employeeIds = $record->sdm_ids ?? [];

                        if (is_string($employeeIds)) {
                            $decoded = json_decode($employeeIds, true);
                            $employeeIds = is_array($decoded)
                                ? $decoded
                                : array_map('intval', explode(',', (string) $employeeIds));
                        }

                        $employeeIds = collect($employeeIds)
                            ->filter(fn($id) => filled($id))
                            ->map(fn($id) => (int) $id)
                            ->unique()
                            ->values()
                            ->all();

                        $employees = empty($employeeIds)
                            ? collect()
                            : \App\Models\Employee::with('user')
                            ->whereIn('id', $employeeIds)
                            ->get()
                            ->sortBy(fn($employee) => $employee->user?->name ?? '');

                        return view('filament.modals.project-employees', [
                            'employees' => $employees,
                        ]);
                    })
                    ->visible(function (ProjectRequest $record): bool {
                        $employeeIds = $record->sdm_ids ?? [];

                        if (is_string($employeeIds)) {
                            $decoded = json_decode($employeeIds, true);
                            $employeeIds = is_array($decoded)
                                ? $decoded
                                : array_filter(explode(',', (string) $employeeIds));
                        }

                        return ! empty($employeeIds);
                    }),

                // Action::make('returnAssets')
                //     ->label('Kembalikan Aset')
                //     ->icon('heroicon-o-arrow-path')
                //     ->color('warning')
                //     ->requiresConfirmation()
                //     ->modalHeading('Kembalikan Semua Aset Proyek')
                //     ->modalSubheading('Apakah Anda yakin ingin mengembalikan semua aset proyek ini ke status Tersedia?')
                //     ->action(function (ProjectRequest $record) {
                //         $assetIds = $record->asset_ids ?? [];
                //         if (!empty($assetIds)) {
                //             \App\Models\Aset::whereIn('id', $assetIds)->update(['status' => 'available']);
                //         }
                //         \Filament\Notifications\Notification::make()
                //             ->title('Berhasil')
                //             ->body('Semua aset proyek telah dikembalikan ke status Tersedia.')
                //             ->success()
                //             ->send();
                //     })
                //     ->visible(function (ProjectRequest $record) {
                //         $assetIds = $record->asset_ids ?? [];
                //         if (empty($assetIds)) return false;
                //         return \App\Models\Aset::whereIn('id', $assetIds)
                //             ->where('status', 'unavailable')
                //             ->exists();
                //     }),

                Action::make('viewRabAwal')
                    ->label('Lihat RAB Awal')
                    ->icon('heroicon-o-document-text')
                    ->tooltip('Lihat Rencana Anggaran Biaya Awal')
                    ->color('gray')
                    ->modalHeading('Rencana Anggaran Biaya Awal')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('fit-content')
                    ->visible(function (ProjectRequest $record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        // Hanya user yang memiliki permission 'view rab' boleh melihat action ini
                        return ($record->rabOperasionalItems()->exists() || $record->rabFeeItems()->exists())
                            && $user->can('view rab awal');
                    })
                    ->modalContent(
                        fn(ProjectRequest $record): View => view('filament.tables.actions.view-rab-awal-modal-content', ['record' => $record])
                    ),

                Action::make('approveLevel1')
                    ->label('Setujui Level 1')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Persetujuan Level 1 - Permintaan Proyek')
                    ->modalSubheading('Apakah Anda yakin ingin menyetujui permintaan proyek ini (Level 1)?')
                    ->modalButton('Ya, Setujui Level 1')
                    ->form([
                        Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->rows(3),
                    ])
                    ->visible(function ($record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        // Approval logic: Only show if L1 is required
                        if (! GeneralSetting::isProjectL1Required()) {
                            return false;
                        }

                        return $record->isPendingLevel1Approval() && $user->can('approve_project_level_1');
                    })
                    ->action(function ($record, array $data) {
                        $record->update([
                            'approval_level_1_status' => 'approved',
                            'approval_level_1_by' => auth()->id(),
                            'approval_level_1_at' => now(),
                            'approval_level_1_notes' => $data['notes'] ?? null,
                        ]);

                        if (! GeneralSetting::isProjectL2Required()) {
                            // Skip L2 and approve directly
                            $record->update(['status' => 'approved']);
                            $record->markAssetsUnavailable();
                            $record->deductBmhpStock();

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Project request telah disetujui (L1 dilakukan, L2 dilewati).')
                                ->success()
                                ->send();
                        } else {
                            $record->update([
                                'approval_level_2_status' => 'pending', // Set level 2 to pending
                            ]);
                            // Kirim notifikasi ke user dengan permission 'approve_project_level_2'
                            $level2Approvers = \App\Models\User::permission('approve_project_level_2')->get();
                            foreach ($level2Approvers as $approver) {
                                $approver->notify(new \App\Notifications\ProjectRequestLevel2Approval($record));
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Project request telah disetujui Level 1. Menunggu persetujuan Level 2.')
                                ->success()
                                ->send();
                        }
                    }),

                Action::make('approveLevel2')
                    ->label('Setujui Level 2')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Persetujuan Level 2 - Permintaan Proyek')
                    ->modalSubheading('Apakah Anda yakin ingin menyetujui permintaan proyek ini (Level 2)? Ini adalah persetujuan akhir.')
                    ->modalButton('Ya, Setujui Level 2')
                    ->form([
                        Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->rows(3),
                    ])
                    ->visible(function ($record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        // Approval logic: Only show if L2 is required
                        if (! GeneralSetting::isProjectL2Required()) {
                            return false;
                        }

                        return $record->isPendingLevel2Approval() && $user->can('approve_project_level_2');
                    })
                    ->action(function ($record, array $data) {
                        $record->update([
                            'approval_level_2_status' => 'approved',
                            'approval_level_2_by' => auth()->id(),
                            'approval_level_2_at' => now(),
                            'approval_level_2_notes' => $data['notes'] ?? null,
                            'status' => 'approved',
                        ]);

                        // Use centralized helpers
                        $record->markAssetsUnavailable();
                        $record->deductBmhpStock();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Project request telah disetujui sepenuhnya (Level 2). Status berubah menjadi Approved.')
                            ->success()
                            ->send();
                    }),

                Action::make('directApprove')
                    ->label('Konfirmasi Proyek')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Aktifkan Proyek')
                    ->modalSubheading('Apakah Anda yakin ingin mengaktifkan proyek ini secara langsung? Stok akan dipotong otomatis.')
                    ->modalButton('Ya, Konfirmasi')
                    ->visible(function ($record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        $l1Required = GeneralSetting::isProjectL1Required();
                        $l2Required = GeneralSetting::isProjectL2Required();

                        // Show if (Pending L1 AND L1 is OFF AND L2 is OFF)
                        if ($record->isPendingLevel1Approval() && !$l1Required && !$l2Required) {
                            return $user->can('approve_project_level_2');
                        }

                        // Show if (Pending L2 AND L2 is OFF)
                        if ($record->isPendingLevel2Approval() && !$l2Required) {
                            return $user->can('approve_project_level_2');
                        }

                        return false;
                    })
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'approval_level_1_status' => 'approved',
                            'approval_level_1_at' => now(),
                            'approval_level_1_by' => auth()->id(),
                            'approval_level_2_status' => 'approved',
                            'approval_level_2_at' => now(),
                            'approval_level_2_by' => auth()->id(),
                        ]);

                        // Use centralized helpers
                        $record->markAssetsUnavailable();
                        $record->deductBmhpStock();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Proyek telah aktif. Stok telah dipotong.')
                            ->success()
                            ->send();
                    }),

                Action::make('rejectApproval')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Permintaan Proyek')
                    ->modalSubheading('Apakah Anda yakin ingin menolak permintaan proyek ini?')
                    ->modalButton('Ya, Tolak')
                    ->form([
                        Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(function ($record) {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        // If multi-level is required
                        $l1Required = GeneralSetting::isProjectL1Required();
                        $l2Required = GeneralSetting::isProjectL2Required();

                        if ($l1Required && $record->isPendingLevel1Approval()) {
                            return $user->can('approve_project_level_1');
                        }

                        if ($l2Required && $record->isPendingLevel2Approval()) {
                            return $user->can('approve_project_level_2');
                        }

                        // If direct flow (one or both are OFF)
                        if (!$l1Required || !$l2Required) {
                            return $record->status === 'pending' && $user->can('approve_project_level_2');
                        }

                        return false;
                    })
                    ->action(function ($record, array $data) {
                        $user = auth()->user();

                        // Determine which level is rejecting
                        if ($record->isPendingLevel1Approval()) {
                            $record->update([
                                'approval_level_1_status' => 'rejected',
                                'approval_level_1_by' => $user->id,
                                'approval_level_1_at' => now(),
                                'approval_level_1_notes' => $data['notes'],
                                'status' => 'rejected',
                            ]);
                        } else {
                            $record->update([
                                'approval_level_2_status' => 'rejected',
                                'approval_level_2_by' => $user->id,
                                'approval_level_2_at' => now(),
                                'approval_level_2_notes' => $data['notes'],
                                'status' => 'rejected',
                            ]);
                        }

                        Notification::make()
                            ->title('Project Request Ditolak')
                            ->body('Project request telah ditolak.')
                            ->danger()
                            ->send();
                    }),

                Action::make('manualDeductStock')
                    ->label('Potong Stok Manual')
                    ->icon('heroicon-o-scissors')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Potong Stok BMHP Manual')
                    ->modalSubheading('Ini akan memotong stok BMHP untuk proyek yang sudah disetujui.')
                    ->visible(function (ProjectRequest $record): bool {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        return $record->isFullyApproved() && !$record->bmhp_stock_deducted && $user->isSuperAdmin();
                    })
                    ->action(function (ProjectRequest $record) {
                        try {
                            $record->deductBmhpStock();

                            Notification::make()
                                ->title('Berhasil')
                                ->body('Stok BMHP telah dipotong manual.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Gagal memotong stok: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('printInvoice')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(ProjectRequest $record): string => route('project-requests.invoice', $record))
                    ->openUrlInNewTab()
                    ->visible(function (ProjectRequest $record): bool {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        return in_array($record->status, ['approved', 'done']) && $user->can('print invoice project');
                    }),

                Action::make('manageClosingRab')
                    ->label(
                        fn(ProjectRequest $record): string => $record->rabClosing()->exists() ? 'Edit RAB Closing' : 'Buat RAB Closing'
                    )
                    ->icon(
                        fn(ProjectRequest $record): string => $record->rabClosing()->exists() ? 'heroicon-o-pencil-square' : 'heroicon-o-document-check'
                    )
                    ->color(
                        fn(ProjectRequest $record): string => $record->rabClosing()->exists() ? 'info' : 'warning'
                    )
                    ->visible(function (ProjectRequest $record): bool {
                        $user = auth()?->user();
                        if (! $user) {
                            return false;
                        }

                        return $record->status === 'approved' && $user->can('rab manage');
                    })
                    ->action(function (ProjectRequest $record) {
                        try {
                            DB::beginTransaction();

                            $totalOperasional = $record->rabOperasionalItems()->sum('total');
                            $totalFee = $record->rabFeeItems()->sum('total');
                            $totalBmhp = $record->projectBmhp()->sum('total');
                            $totalAnggaranAwal = $totalOperasional + $totalFee + $totalBmhp;
                            $jumlahPesertaAwal = $record->jumlah;

                            $rabClosing = $record->rabClosing;
                            if (! $rabClosing) {
                                $rabClosing = $record->rabClosing()->create([
                                    'closing_date' => now(),
                                    'status' => 'draft',
                                    'total_anggaran' => $totalAnggaranAwal,
                                    'jumlah_peserta_awal' => $jumlahPesertaAwal,
                                ]);
                            } elseif ($rabClosing->status === 'draft') {
                                // Update header info
                                $rabClosing->update([
                                    'total_anggaran' => $totalAnggaranAwal,
                                    'jumlah_peserta_awal' => $jumlahPesertaAwal,
                                ]);
                            }

                            // Sync Items (Only if draft)
                            if ($rabClosing->status === 'draft') {
                                // 1. Operasional Items (Append missing)
                                foreach ($record->rabOperasionalItems as $itemAwal) {
                                    $exists = $rabClosing->operasionalItems()->where('description', $itemAwal->description)->exists();
                                    if (! $exists) {
                                        $rabClosing->operasionalItems()->create([
                                            'description' => $itemAwal->description,
                                            'price' => $itemAwal->total,
                                        ]);
                                    }
                                }

                                // 2. Fee Items (Append missing)
                                foreach ($record->rabFeeItems as $itemAwal) {
                                    $exists = $rabClosing->feePetugasItems()->where('description', $itemAwal->description)->exists();
                                    if (! $exists) {
                                        $rabClosing->feePetugasItems()->create([
                                            'description' => $itemAwal->description,
                                            'price' => $itemAwal->total,
                                        ]);
                                    }
                                }

                                // 3. BMHP Items (Sync/Overwrite since it's read-only reference or logistics input)
                                foreach ($record->projectBmhp as $itemBmhp) {
                                    $exists = $rabClosing->bmhpItems()->where('bmhp_id', $itemBmhp->bmhp_id)->exists();
                                    if (! $exists) {
                                        $rabClosing->bmhpItems()->create([
                                            'bmhp_id' => $itemBmhp->bmhp_id,
                                            'name' => $itemBmhp->bmhp->name ?? 'Unknown',
                                            'satuan' => $itemBmhp->bmhp->satuan ?? '-',
                                            'jumlah_rencana' => $itemBmhp->jumlah_rencana,
                                            'harga_satuan' => $itemBmhp->harga_satuan,
                                            'total' => $itemBmhp->total, // Initial used total equals planned total (sisa = 0)
                                            'pcs_per_unit_snapshot' => $itemBmhp->pcs_per_unit_snapshot ?? 1,
                                        ]);
                                    } else {
                                        // Update planned info but recalculate used total based on existing sisa
                                        $existingItem = $rabClosing->bmhpItems()->where('bmhp_id', $itemBmhp->bmhp_id)->first();
                                        $newTotal = ($itemBmhp->jumlah_rencana - $existingItem->jumlah_sisa) * $itemBmhp->harga_satuan;

                                        $existingItem->update([
                                            'jumlah_rencana' => $itemBmhp->jumlah_rencana,
                                            'harga_satuan' => $itemBmhp->harga_satuan,
                                            'total' => $newTotal,
                                            'pcs_per_unit_snapshot' => $itemBmhp->pcs_per_unit_snapshot ?? 1,
                                        ]);
                                    }
                                }
                            }

                            DB::commit();

                            return redirect()->to(RabClosingResource::getUrl('edit', ['record' => $rabClosing->id]));
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Terjadi Kesalahan')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected static function syncAssignedStaffFeeItems(Get $get, Set $set, mixed $state): void
    {
        $selectedIds = collect(is_array($state) ? $state : [])
            ->filter(fn($id) => filled($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $existingItems = collect($get('rabFeeItems') ?? []);
        if ($selectedIds->isEmpty()) {
            // Jika tidak ada pegawai internal dipilih, pertahankan hanya item manual (external).
            $manualOnly = $existingItems
                ->filter(fn($item) => ! filled($item['assigned_employee_id'] ?? null))
                ->values()
                ->all();
            $set('rabFeeItems', $manualOnly);

            return;
        }

        $staffNames = Employee::query()
            ->whereIn('employees.id', $selectedIds->all())
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->pluck('users.name', 'employees.id')
            ->toArray();
        $staffNameValues = collect($staffNames)->values()->all();

        // 1) Ambil item linked yang sudah punya marker assigned_employee_id.
        $existingByStaffId = $existingItems
            ->filter(fn($item) => filled($item['assigned_employee_id'] ?? null))
            ->keyBy(fn($item) => (int) $item['assigned_employee_id']);

        // 2) Fallback untuk data lama tanpa marker: cocokkan berdasarkan description == nama staff.
        $unassignedRows = $existingItems
            ->filter(fn($item) => ! filled($item['assigned_employee_id'] ?? null))
            ->values();
        foreach ($selectedIds as $staffId) {
            if ($existingByStaffId->has($staffId)) {
                continue;
            }

            $name = $staffNames[$staffId] ?? null;
            if (! filled($name)) {
                continue;
            }

            $matchIndex = $unassignedRows->search(function ($row) use ($name) {
                return isset($row['description']) && trim((string) $row['description']) === $name;
            });

            if ($matchIndex !== false) {
                $existingByStaffId->put($staffId, $unassignedRows[$matchIndex]);
                $unassignedRows->forget($matchIndex);
                $unassignedRows = $unassignedRows->values();
            }
        }

        // 3) Baris manual external: row tanpa assigned_employee_id dan description bukan nama staff internal terpilih.
        $manualItems = $existingItems
            ->filter(function ($item) use ($staffNameValues) {
                if (filled($item['assigned_employee_id'] ?? null)) {
                    return false;
                }

                $desc = trim((string) ($item['description'] ?? ''));

                return $desc === '' || ! in_array($desc, $staffNameValues, true);
            })
            ->values();

        // 4) Sync linked items sesuai pilihan pegawai internal.
        $linkedItems = $selectedIds->map(function (int $staffId) use ($existingByStaffId, $staffNames) {
            $existing = $existingByStaffId->get($staffId, []);
            $qty = max((int) ($existing['qty_aset'] ?? 1), 1);
            $hargaRaw = $existing['harga_sewa'] ?? null;
            $harga = self::cleanMoneyValue($hargaRaw);

            return [
                'assigned_employee_id' => $staffId,
                'description' => $staffNames[$staffId] ?? "Pegawai #{$staffId}",
                'qty_aset' => $qty,
                'harga_sewa' => $hargaRaw,
                'total' => number_format($qty * $harga, 0, '.', ','),
            ];
        });

        $set('rabFeeItems', $linkedItems->concat($manualItems)->values()->all());
    }

    protected static function cleanMoneyValue(?string $value): int|float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) preg_replace('/[^\d]/', '', $value);
    }

    protected static function updateRowTotal(Get $get, Set $set): void
    {
        $qty = (int) ($get('qty_aset') ?? 0);
        $harga = self::cleanMoneyValue($get('harga_sewa'));
        $set('total', number_format($qty * $harga, 0, '.', ','));
    }

    protected static function updateBmhpRowTotal(Get $get, Set $set): void
    {
        $qty = (int) ($get('qty') ?? 0);
        $purchaseType = (string) ($get('purchase_type') ?? 'pcs');
        $pcsPerUnit = (int) ($get('pcs_per_unit_snapshot') ?? 0);
        $harga = self::cleanMoneyValue($get('harga_satuan'));

        // Calculate total pcs for database record (jumlah_rencana)
        $totalPcs = 0;
        if ($purchaseType === 'pcs') {
            $totalPcs = $qty;
        } else {
            $multiplier = $pcsPerUnit > 0 ? $pcsPerUnit : 1;
            $totalPcs = $qty * $multiplier;
        }
        $set('jumlah_rencana', $totalPcs);

        // Subtotal calculation (qty * price)
        $set('total', number_format($qty * $harga, 0, '.', ','));
    }

    protected static function normalizeAssetCode(?string $code): string
    {
        if (! filled($code)) {
            return '-';
        }

        $parts = explode('/', (string) $code);

        if (count($parts) >= 4) {
            $code = "{$parts[0]}/{$parts[count($parts) - 2]}/{$parts[count($parts) - 1]}";
        }

        $normalizedParts = array_map(
            fn($part) => Str::upper((string) preg_replace('/\s+/', '-', trim((string) $part))),
            explode('/', (string) $code)
        );

        return implode('/', $normalizedParts);
    }

    /**
     * Enable editing Participants from the relation tab, even in the "view" page.
     */
    public static function getRelations(): array
    {
        // By default, all relation managers registered here are available
        // on both edit and view pages (if the page uses HasRelationManagers trait).
        // To explicitly allow editing from the view page, ensure the View page
        // uses HasRelationManagers and that the ParticipantsRelationManager
        // defines the necessary create & edit actions.
        return [
            RelationManagers\ParticipantsRelationManager::class,
        ];
    }

    protected static function getDaysBetween($start, $end): int
    {
        if (! $start || ! $end) {
            return 1;
        }
        try {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

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
            'view' => Pages\ViewProjectRequest::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view projects');
    }
}
