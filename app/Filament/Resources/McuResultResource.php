<?php

namespace App\Filament\Resources;

use App\Filament\Resources\McuResultResource\Pages;
use App\Filament\Resources\McuResultResource\RelationManagers;
use App\Models\McuResult;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class McuResultResource extends Resource
{
    protected static ?string $model = McuResult::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Project MCU';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informasi Dasar')
                        ->schema([
                            Forms\Components\Select::make('project_request_id')
                                ->relationship('projectRequest', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Proyek MCU')
                                ->live(), // <-- PENTING: Membuat form bereaksi saat field ini berubah

                            Forms\Components\Select::make('participant_id')
                                ->required()
                                ->label('Peserta')
                                ->searchable()
                                // Opsi dropdown ini sekarang dinamis
                                ->options(function (Get $get) {
                                    $projectId = $get('project_request_id');
                                    if ($projectId) {
                                        // Ambil HANYA peserta dari proyek yang sudah dipilih
                                        return \App\Models\Participant::where('project_request_id', $projectId)
                                            ->pluck('name', 'id');
                                    }
                                    // Jika belum ada proyek yang dipilih, kembalikan array kosong
                                    return [];
                                }),

                            Forms\Components\TextInput::make('no_mcu')->required()->label('No. MCU'),
                            Forms\Components\DatePicker::make('tanggal_mcu')->required()->label('Tanggal MCU'),
                        ]),

                    Forms\Components\Wizard\Step::make('Pemeriksaan Fisik')
                        ->schema([
                            Forms\Components\KeyValue::make('hasil_pemeriksaan_vital_sign')->label('Vital Sign (e.g., Berat Badan: 82 Kg)')->reorderable(),
                            Forms\Components\Repeater::make('hasil_pemeriksaan_fisik_dokter')->label('Pemeriksaan Fisik')
                                ->schema([
                                    Forms\Components\TextInput::make('area')->label('Area Tubuh')->required(),
                                    Forms\Components\TextInput::make('hasil')->label('Hasil')->required(),
                                    Forms\Components\TextInput::make('kesimpulan')->label('Kesimpulan'),
                                ])->columns(3),
                        ]),

                    Forms\Components\Wizard\Step::make('Lampiran & Gambar')
                        ->schema([
                            Forms\Components\Repeater::make('attachments')
                                ->relationship()
                                ->schema([
                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('Upload File')
                                        ->disk('public')
                                        ->directory('mcu-attachments')
                                        ->required()
                                        ->preserveFilenames(),
                                    Forms\Components\TextInput::make('description')
                                        ->label('Deskripsi (e.g., Foto Thorax)')
                                ])
                        ]),

                    Forms\Components\Wizard\Step::make('Kesimpulan & Saran')
                        ->schema([
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.kesimpulan')->label('Kesimpulan'),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.catatan')->label('Catatan'),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.saran')->label('Saran'),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('participant.name')->searchable()->sortable()->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('projectRequest.name')->searchable()->label('Project'),
                Tables\Columns\TextColumn::make('no_mcu')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mcu')->date()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_result')
                    ->label('Lihat Hasil')
                    ->url(fn(McuResult $record): string => static::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMcuResults::route('/'),
            'create' => Pages\CreateMcuResult::route('/create'),
            'edit' => Pages\EditMcuResult::route('/{record}/edit'),
            'view' => Pages\ViewMcuResult::route('/{record}/view'),
        ];
    }
}
