<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreadmillCheckResource\Pages;
use App\Models\TreadmillCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Actions\Action;

class TreadmillCheckResource extends Resource
{
    protected static ?string $model = TreadmillCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan Treadmill';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
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
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id'))),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Pemeriksaan')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('metode')->default('Bruce'),
                        Forms\Components\TextInput::make('ekg_resting')->label('EKG Resting')->default('Normal x/menit'),
                        Fieldset::make('EKG Exercise')->schema([
                            Forms\Components\TextInput::make('ekg_exercise_st_change')->label('ST-T Change Segmen')->default('--'),
                            Forms\Components\TextInput::make('ekg_exercise_aritmia')->label('Aritmia')->default('--'),
                        ]),
                        Fieldset::make('Tekanan Darah')->schema([
                            Forms\Components\TextInput::make('td_awal')->label('Awal')->suffix('mm/Hg'),
                            Forms\Components\TextInput::make('td_tertinggi')->label('Tertinggi')->suffix('mm/Hg'),
                        ]),
                        Forms\Components\TextInput::make('indikasi_berhenti')->default('85 % target Heart Rate tercapai'),
                        Forms\Components\TextInput::make('target_hr')->label('Target HR')->placeholder('x/menit ( % ) dari Maksimal HR'),
                        Forms\Components\TextInput::make('tercapai_hr')->label('Tercapai HR')->placeholder('x/menit'),
                        Fieldset::make('Lama Test')->columns(2)->schema([
                            Forms\Components\TextInput::make('lama_tes_menit')->label('Menit')->numeric(),
                            Forms\Components\TextInput::make('lama_tes_detik')->label('Detik')->numeric()->maxValue(59),
                        ]),
                        Forms\Components\TextInput::make('kapasitas_aerobik')->label('Kapasitas Aerobik')->suffix('Mets'),
                        Forms\Components\TextInput::make('kelas_fungsional')->label('Kelas Fungsional')->default('I'),
                        Forms\Components\TextInput::make('tingkat_kebugaran')->label('Tingkat Kebugaran'),
                    ]),

                Section::make('Kesimpulan & Saran')
                    ->schema([
                        Forms\Components\Textarea::make('kesimpulan')->default('Negative ischemic response'),
                        Forms\Components\Textarea::make('saran')->rows(4)->default("Olah raga teratur\nDiet Seimbang\nLakukan pemeriksaan treadmill setahun kemudian"),
                    ]),

                Section::make('Cardiologist & Lampiran')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('cardiologist')->label('Cardiologist')->default('dr. Muhammad Aditya, Sp.JP'),
                        Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD Dokter')->image()->disk('public')->directory('ttd-treadmill'),
                        Forms\Components\FileUpload::make('gambar_hasil_treadmill')->label('Upload Gambar Hasil Treadmill')->image()->disk('public')->directory('hasil-treadmill')->required()->columnSpanFull(),
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
                    ->url(fn(TreadmillCheck $record): string => route('treadmill.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTreadmillChecks::route('/'),
            'create' => Pages\CreateTreadmillCheck::route('/create'),
            'edit' => Pages\EditTreadmillCheck::route('/{record}/edit'),
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
