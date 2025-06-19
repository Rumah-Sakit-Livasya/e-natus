<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectRequestResource\Pages;
use App\Models\Aset;
use App\Models\Client;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Forms\Components\{DatePicker, Select, Textarea, TextInput};
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
                Select::make('sdm_id')
                    ->label('SDM')
                    ->options(\App\Models\SDM::pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama SDM')
                            ->required(),
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

                Textarea::make('keterangan')->label('Keterangan')->nullable(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama Proyek')->searchable()->sortable(),
            TextColumn::make('client.name')->label('Klien')->sortable(),
            TextColumn::make('pic')->label('PIC'),
            TextColumn::make('sdm.name')->label('SDM')->sortable(),
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
                    ->modalHeading('Daftar Aset')
                    ->modalSubheading('Berikut adalah aset yang terkait dengan project ini.')
                    ->modalButton('Tutup')
                    ->action(fn() => null)
                    ->modalContent(fn($record) => new HtmlString(
                        Livewire::mount('project-asset-table', [
                            'assetIds' => $record->asset_ids ?? [],
                        ])
                    )),

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
