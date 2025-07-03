<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RabClosingResource\Pages;
use App\Filament\Resources\RabClosingResource\RelationManagers;
use App\Models\RabClosing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Pages\CompareRab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\NumericInput;


class RabClosingResource extends Resource
{
    protected static ?string $model = RabClosing::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Project'; // Kelompokkan menu
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // Placeholder lebih cocok untuk nama proyek karena tidak akan pernah jadi input
                        Forms\Components\Placeholder::make('project_name')
                            ->label('Proyek')
                            ->content(fn(?RabClosing $record): string => $record?->projectRequest->name ?? '-'),

                        Forms\Components\DatePicker::make('closing_date')
                            ->required()->label('Tanggal Closing'),

                        Forms\Components\Select::make('status')
                            ->options(['draft' => 'Draft', 'final' => 'Final'])
                            ->disabled()
                            ->required(),
                    ])->columns(3),

                // --- INI PERUBAHANNYA ---
                // Mengganti Placeholder menjadi TextInput yang dinonaktifkan
                Forms\Components\Group::make()
                    ->schema([
                        TextInput::make('total_anggaran')
                            ->label('Total Anggaran (Otomatis)')
                            ->prefix('Rp')
                            ->disabled(), // Dinonaktifkan agar tidak bisa di-input manual

                        TextInput::make('total_realisasi')
                            ->label('Total Realisasi (Otomatis)')
                            ->prefix('Rp')
                            ->disabled(),

                        TextInput::make('selisih')
                            ->label('Selisih (Otomatis)')
                            ->prefix('Rp')
                            ->disabled(),
                    ])->columns(3),
                // --- AKHIR PERUBAHAN ---

                Repeater::make('items')
                    ->label('Item Anggaran Closing')
                    ->relationship()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('description')->required(),

                        // --- PERUBAHAN DI SINI ---
                        TextInput::make('qty')
                            ->required()
                            ->numeric() // <-- Membuatnya hanya menerima angka
                            ->live(),

                        TextInput::make('satuan')->required(),

                        // --- DAN DI SINI ---
                        TextInput::make('harga_satuan')
                            ->prefix('Rp')
                            ->required()
                            ->numeric() // <-- Membuatnya hanya menerima angka
                            ->live(),

                        TextInput::make('total_anggaran')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled()
                            ->placeholder(fn($get) => number_format($get('qty') * $get('harga_satuan'), 0, ',', '.')),
                    ])
                    ->columns(5)
                    ->reorderableWithButtons()
                    ->addActionLabel('Tambah Item')
                    ->deleteAction(fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation())
                    ->disabled(fn(?RabClosing $record) => $record?->status === 'final'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('projectRequest.name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Proyek'),

                TextColumn::make('closing_date')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tanggal Closing'),

                TextColumn::make('total_anggaran')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_realisasi')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('selisih')
                    ->numeric()
                    ->money('IDR')
                    ->sortable()
                    ->color(fn(int $state): string => $state >= 0 ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tambahkan action untuk ke halaman perbandingan
                Tables\Actions\Action::make('compare')
                    ->label('Bandingkan')
                    ->icon('heroicon-o-scale')
                    // Panggil getUrl() dari class CompareRab yang sudah di-import
                    ->url(fn(RabClosing $record): string => CompareRab::getUrl(['record' => $record->project_request_id]))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRabClosings::route('/'),
            'create' => Pages\CreateRabClosing::route('/create'),
            'edit' => Pages\EditRabClosing::route('/{record}/edit'),
        ];
    }
}
