<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BmhpCluster;
use App\Filament\Resources\ProjectBmhpRemainderResource\Pages;
use App\Models\RabClosingBmhpItem;
use App\Models\RabClosing;
use App\Models\Bmhp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectBmhpRemainderResource extends Resource
{
    protected static ?string $model = RabClosingBmhpItem::class;

    protected static ?string $cluster = BmhpCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'BHP Sisa Project';
    protected static ?string $pluralModelLabel = 'BHP Sisa Project';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Proyek & Justifikasi')
                    ->description('Pilih proyek untuk melihat justifikasi dana/operasional dari tim keuangan.')
                    ->schema([
                        Forms\Components\Select::make('rab_closing_id')
                            ->label('Pilih Proyek (Closing)')
                            ->options(RabClosing::with('projectRequest')->get()->pluck('projectRequest.name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $closing = RabClosing::find($state);
                                    $set('justifikasi_display', $closing?->justifikasi ?? 'Tidak ada justifikasi.');
                                } else {
                                    $set('justifikasi_display', null);
                                }
                            }),
                        Forms\Components\Placeholder::make('justifikasi_preview')
                            ->label('Catatan Justifikasi dari Finance')
                            ->content(fn(Forms\Get $get) => new \Illuminate\Support\HtmlString($get('justifikasi_display') ?? $get('record.rabClosing.justifikasi') ?? '-'))
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Detail Sisa BHP')
                    ->description('Masukkan rincian barang dan jumlah yang kembali ke logistik.')
                    ->schema([
                        Forms\Components\Select::make('bmhp_id')
                            ->label('Pilih BMHP (Master Data)')
                            ->options(Bmhp::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->native(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $bmhp = Bmhp::find($state);
                                    if ($bmhp) {
                                        $set('name', $bmhp->name);
                                        $set('satuan', $bmhp->satuan);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama/Deskripsi Barang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('satuan')
                            ->label('Satuan')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('jumlah_rencana')
                            ->label('Jumlah Keluar (Rencana)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                        Forms\Components\TextInput::make('jumlah_sisa')
                            ->label('Jumlah Sisa (Kembali)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                        Forms\Components\TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::updateTotal($get, $set)),
                        Forms\Components\TextInput::make('total')
                            ->label('Total Biaya Terpakai')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->helperText('Dihitung otomatis: (Rencana - Sisa) x Harga Satuan.'),
                    ])->columns(2),
            ]);
    }

    public static function updateTotal(Forms\Get $get, Forms\Set $set): void
    {
        $rencana = (float) ($get('jumlah_rencana') ?? 0);
        $sisa = (float) ($get('jumlah_sisa') ?? 0);
        $harga = (float) ($get('harga_satuan') ?? 0);

        $total = ($rencana - $sisa) * $harga;
        $set('total', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rabClosing.projectRequest.name')
                    ->label('Proyek')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_rencana')
                    ->label('Rencana')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_sisa')
                    ->label('Sisa (Kembali)')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Input Terakhir')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rab_closing_id')
                    ->label('Proyek')
                    ->options(RabClosing::with('projectRequest')->get()->pluck('projectRequest.name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Update Sisa')
                    ->modalHeading('Update Sisa Barang Project'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectBmhpRemainders::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view bmhp');
    }
}
