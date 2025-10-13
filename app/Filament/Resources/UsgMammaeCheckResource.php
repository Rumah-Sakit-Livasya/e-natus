<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsgMammaeCheckResource\Pages;
use App\Models\UsgMammaeCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;

class UsgMammaeCheckResource extends Resource
{
    protected static ?string $model = UsgMammaeCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan USG Mammae';

    public static function form(Form $form): Form
    {
        $defaultTemuan = "- Tampak parenkim mammae dominan glandular\n"
            . "- Tak tampak lesi massa/kistik\n"
            . "- Kutis dan subkutis normal\n"
            . "- Tak tampak retraksi papilla mammae kanan\n"
            . "- Tak tampak kalsifikasi";

        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('participant_id')
                            ->label('Nama Peserta')
                            ->relationship('participant', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                    $set('instansi', $p->department); // Tambahkan ini
                                } else {
                                    $set('tgl_lahir', null);
                                    $set('usia', null);
                                    $set('jenis_kelamin', null);
                                    $set('instansi', null);
                                }
                            })
                            ->required()
                            // =======================================================
                            //           PERUBAHAN UTAMA ADA DI DUA BARIS INI
                            // =======================================================
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id'))),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Pemeriksaan USG Mammae')
                    ->schema([
                        Forms\Components\Textarea::make('mammae_kanan')->label('Mammae Kanan')->rows(6)->default($defaultTemuan),
                        Forms\Components\Textarea::make('mammae_kiri')->label('Mammae Kiri')->rows(6)->default($defaultTemuan),
                        Forms\Components\TextInput::make('catatan_tambahan')
                            ->label('Catatan Tambahan')
                            ->default('Tak tampak limfadenopathy axilla bilateral'),
                        Forms\Components\Textarea::make('kesimpulan')->label('Kesimpulan')->rows(3)
                            ->default("1. Mammae kanan kiri tak tampak kelainan (Negative Finding-BIRADS 1)2. Tak tampak limfadenopathy axilla bilateral"),
                    ]),

                Section::make('Radiologist & Lampiran')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('radiologist')->label('Radiologist')->default('dr. Fitri Lutfia, Sp.Rad'),
                        Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD Dokter')->image()->disk('public')->directory('ttd-usg-mammae'),
                        Forms\Components\FileUpload::make('gambar_hasil_usg')->label('Upload Gambar Hasil USG')->image()->disk('public')->directory('hasil-usg-mammae')->required()->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rm')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(UsgMammaeCheck $record): string => route('usg-mammae.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsgMammaeChecks::route('/'),
            'create' => Pages\CreateUsgMammaeCheck::route('/create'),
            'edit' => Pages\EditUsgMammaeCheck::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }
        return $user->can('view hasil mcu');
    }
}
