<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokterResource\Pages;
use App\Models\Dokter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DokterResource extends Resource
{
    protected static ?string $cluster = \App\Filament\Clusters\MasterDataCluster::class;

    protected static ?string $model = Dokter::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Dokter';
    protected static ?string $pluralModelLabel = 'Master Dokter';
    protected static ?string $slug = 'dokter';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Dokter')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('spesialisasi')
                ->label('Spesialisasi')
                ->maxLength(255),
            Forms\Components\TextInput::make('no_str')
                ->label('No. STR')
                ->maxLength(255),
            Forms\Components\TextInput::make('no_sip')
                ->label('No. SIP')
                ->maxLength(255),
            Forms\Components\FileUpload::make('scan_str_sip')
                ->label('Upload Scan STR/SIP')
                ->disk('public')
                ->directory('dokter-str-sip')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ])
                ->maxSize(5120)
                ->helperText('Opsional. Format: PDF/JPG/PNG/WEBP, maksimal 5MB.'),
            Forms\Components\FileUpload::make('tanda_tangan')
                ->label('TTD Dokter')
                ->image()
                ->disk('public')
                ->directory('ttd-dokter')
                ->imagePreviewHeight('100'),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dokter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('spesialisasi')
                    ->label('Spesialisasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_str')
                    ->label('No. STR')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_sip')
                    ->label('No. SIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scan_str_sip')
                    ->label('Scan STR/SIP')
                    ->formatStateUsing(fn($state) => filled($state) ? 'Lihat File' : '-')
                    ->url(fn($record) => filled($record->scan_str_sip) ? asset('storage/' . $record->scan_str_sip) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\ImageColumn::make('tanda_tangan')
                    ->label('TTD')
                    ->disk('public'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokters::route('/'),
            'create' => Pages\CreateDokter::route('/create'),
            'edit' => Pages\EditDokter::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->can('view dokters') || $user->can('view hasil mcu');
    }
}
