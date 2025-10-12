<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpirometryCheckResource\Pages;
use App\Models\SpirometryCheck;
use App\Models\Participant;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;

class SpirometryCheckResource extends Resource
{
    protected static ?string $model = SpirometryCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan Spirometri';

    // Fungsi helper untuk kalkulasi agar tidak berulang
    private static function updateCalculations(Get $get, Set $set): void
    {
        $vc_nilai = (float) $get('vc_nilai');
        $vc_prediksi = (float) $get('vc_prediksi');
        $fvc_nilai = (float) $get('fvc_nilai');
        $fvc_prediksi = (float) $get('fvc_prediksi');
        $fev1_nilai = (float) $get('fev1_nilai');
        $fev1_prediksi = (float) $get('fev1_prediksi');

        $set('vc_percent', $vc_prediksi > 0 ? ($vc_nilai / $vc_prediksi) * 100 : 0);
        $set('fvc_percent', $fvc_prediksi > 0 ? ($fvc_nilai / $fvc_prediksi) * 100 : 0);
        $set('fev1_percent', $fev1_prediksi > 0 ? ($fev1_nilai / $fev1_prediksi) * 100 : 0);
        $set('fev1_fvc_nilai', $fvc_nilai > 0 ? ($fev1_nilai / $fvc_nilai) : 0);
        $set('fev1_fvc_prediksi', $fvc_prediksi > 0 ? ($fev1_prediksi / $fvc_prediksi) : 1); // Prediksi/Prediksi = 1 (atau 100%)
        $set('fev1_fvc_percent', $get('fev1_fvc_prediksi') > 0 ? ($get('fev1_fvc_nilai') / $get('fev1_fvc_prediksi')) * 100 : 0);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('participant_id')->label('Nama Peserta')->relationship('participant', 'name')->searchable()->preload()->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                }
                            })->required(),
                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Pelaksanaan')->default(now()),
                    ]),

                Section::make('Hasil Pemeriksaan')
                    ->columns(4)
                    ->schema([
                        Forms\Components\Placeholder::make('pemeriksaan_header')->label('Pemeriksaan')->columnSpan(1),
                        Forms\Components\Placeholder::make('nilai_header')->label('Nilai')->columnSpan(1),
                        Forms\Components\Placeholder::make('prediksi_header')->label('Prediksi')->columnSpan(1),
                        Forms\Components\Placeholder::make('percent_header')->label('%')->columnSpan(1),

                        // Best VC
                        Forms\Components\Placeholder::make('vc_label')->label('Best VC (ml)'),
                        Forms\Components\TextInput::make('vc_nilai')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('vc_prediksi')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('vc_percent')->label(false)->numeric()->readOnly()->suffix('%'),

                        // Best FVC
                        Forms\Components\Placeholder::make('fvc_label')->label('Best FVC (ml)'),
                        Forms\Components\TextInput::make('fvc_nilai')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('fvc_prediksi')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('fvc_percent')->label(false)->numeric()->readOnly()->suffix('%'),

                        // Best FEV1
                        Forms\Components\Placeholder::make('fev1_label')->label('Best FEV1 (ml)'),
                        Forms\Components\TextInput::make('fev1_nilai')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('fev1_prediksi')->label(false)->numeric()->live(onBlur: true)->afterStateUpdated(fn(Get $get, Set $set) => self::updateCalculations($get, $set)),
                        Forms\Components\TextInput::make('fev1_percent')->label(false)->numeric()->readOnly()->suffix('%'),

                        // FEV1/FVC
                        Forms\Components\Placeholder::make('fev1_fvc_label')->label('FEV1 / FVC (%)'),
                        Forms\Components\TextInput::make('fev1_fvc_nilai')->label(false)->numeric()->readOnly(),
                        Forms\Components\TextInput::make('fev1_fvc_prediksi')->label(false)->numeric()->readOnly(),
                        Forms\Components\TextInput::make('fev1_fvc_percent')->label(false)->numeric()->readOnly()->suffix('%'),
                    ]),

                Section::make('Kesan & Saran')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('kesan'),
                        Forms\Components\Textarea::make('saran'),
                    ]),

                Section::make('Dokter & Lampiran')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('dokter_pemeriksa')->label('Dokter Pemeriksa')->default('dr. Daniel Pratama, Sp.P'),
                        Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD Dokter')->image()->disk('public')->directory('ttd-spirometri'),
                        Forms\Components\FileUpload::make('gambar_hasil_spirometri')->label('Upload Gambar Hasil Spirometri')->image()->disk('public')->directory('hasil-spirometri')->required()->columnSpanFull(),
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
                    ->url(fn(SpirometryCheck $record): string => route('spirometri.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array // Sederhanakan navigasi
    {
        return [
            'index' => Pages\ListSpirometryChecks::route('/'),
            'create' => Pages\CreateSpirometryCheck::route('/create'),
            'edit' => Pages\EditSpirometryCheck::route('/{record}/edit'),
        ];
    }
}
