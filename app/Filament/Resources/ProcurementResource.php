<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcurementResource\Pages;
use App\Filament\Resources\ProcurementResource\RelationManagers;
use App\Models\Procurement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcurementResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\AsetCluster::class;

    protected static ?string $model = Procurement::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Pengadaan';
    protected static ?string $navigationLabel = 'Procurement';
    protected static ?string $pluralModelLabel = 'Procurement';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\DatePicker::make('tanggal_pengajuan')
                ->label('Tanggal Pengajuan')
                ->required(),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(3)
                ->maxLength(65535),

            Forms\Components\Repeater::make('items')
                ->label('Daftar Barang')
                // ->dehydrated(false)
                ->schema([
                    Forms\Components\TextInput::make('nama_barang')
                        ->label('Nama Barang')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('unit')
                        ->label('Permintaan Unit')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('harga_pengajuan')
                        ->label('Harga')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $set('jumlah', ($state ?? 0) * ($get('qty_pengajuan') ?? 0));
                        }),

                    Forms\Components\TextInput::make('qty_pengajuan')
                        ->label('Qty')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $set('jumlah', ($get('harga_pengajuan') ?? 0) * ($state ?? 0));
                        }),

                    Forms\Components\TextInput::make('satuan')
                        ->label('Satuan')
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->disabled()
                        ->dehydrated(false)
                        ->reactive()
                        ->afterStateHydrated(function ($state, $set, $get) {
                            $set('jumlah', ($get('harga_pengajuan') ?? 0) * ($get('qty_pengajuan') ?? 0));
                        })
                ])
                ->columns(6)
                ->createItemButtonLabel('Tambah Barang')
                ->minItems(1)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // ➋ Kolom utama dalam satu baris
            TextColumn::make('tanggal_pengajuan')
                ->label('Tanggal Pengajuan')
                ->date()
                ->sortable(),
            TextColumn::make('keterangan')
                ->label('Keterangan')
                ->limit(50)
                ->wrap(),
            TextColumn::make('status_realisasi')
                ->label('Status Realisasi')
                ->getStateUsing(function ($record) {
                    return $record->realisations()->exists() ? 'SUDAH REALISASI' : 'BELUM ADA';
                })
                ->badge()
                ->color(fn($state) => $state === 'SUDAH REALISASI' ? 'success' : 'gray'),
        ])
            ->actions([
                Action::make('viewProcurementItems')
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat Item')
                    ->label('Lihat Item')
                    ->modalHeading('Daftar Item')
                    ->modalSubheading('Berikut adalah item yang terkait dengan procurement ini.')
                    ->modalButton('Tutup')
                    ->action(fn() => null)
                    ->modalContent(fn($record) => new HtmlString(
                        Livewire::mount('procurement-items-table', [
                            'procurementId' => $record->id,
                            // 'items' => $record->items->toArray(),
                        ])
                    )),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    // {
    //     return parent::getEloquentQuery()->with('items');
    // }

    public static function getRelations(): array
    {
        return [
            ProcurementResource\RelationManagers\ProcurementItemsRelationManager::class,
            ProcurementResource\RelationManagers\RealisationsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProcurements::route('/'),
            'create' => Pages\CreateProcurement::route('/create'),
            'edit' => Pages\EditProcurement::route('/{record}/edit'),
            'view' => Pages\ViewProcurement::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view procurement');
    }
}
