<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabCheckResource\Pages;
use App\Models\LabCheck;
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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Actions\Action;

class LabCheckResource extends Resource
{
    protected static ?string $model = LabCheck::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Pemeriksaan';
    protected static ?string $pluralModelLabel = 'Pemeriksaan Lab';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pasien & Pemeriksaan')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('participant_id')->label('Nama Peserta')->relationship('participant', 'name')->searchable()->preload()->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $p = Participant::find($state);
                                    $set('tgl_lahir', Carbon::parse($p->date_of_birth)->translatedFormat('j F Y'));
                                    $set('usia', Carbon::parse($p->date_of_birth)->age);
                                    $set('jenis_kelamin', $p->gender);
                                    $set('instansi', $p->department);
                                } else {
                                    $set('tgl_lahir', null);
                                    $set('usia', null);
                                    $set('jenis_kelamin', null);
                                    $set('instansi', null);
                                }
                            })
                            ->required()
                            // Ditambahkan agar bisa menerima data dari URL
                            ->default(request('participant_id'))
                            ->disabled(filled(request('participant_id')))
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('no_rm')->label('No. RM'),
                        Forms\Components\TextInput::make('no_lab')->label('No. Lab'),
                        Forms\Components\TextInput::make('instansi')->label('Instansi'),
                        Forms\Components\DatePicker::make('tanggal_pemeriksaan')->label('Tanggal Pemeriksaan')->default(now()),
                        Forms\Components\TextInput::make('tgl_lahir')->label('Tanggal Lahir')->readOnly(),
                        Forms\Components\TextInput::make('usia')->label('Usia')->suffix('Tahun')->readOnly(),
                        Forms\Components\TextInput::make('jenis_kelamin')->label('Jenis Kelamin')->readOnly(),
                    ]),

                // =======================================================
                //           PERUBAHAN UTAMA: MENGGUNAKAN TABS
                // =======================================================
                Tabs::make('Hasil Pemeriksaan Lab')->tabs([
                    Tab::make('Hematologi & Urinalisa')->schema([
                        Grid::make(2)->schema([
                            // Kolom Kiri
                            Forms\Components\Group::make()->schema([
                                Section::make('Hematologi Lengkap')->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('hemoglobin'),
                                        Forms\Components\TextInput::make('leukosit'),
                                        Forms\Components\TextInput::make('trombosit'),
                                        Forms\Components\TextInput::make('hematokrit'),
                                        Forms\Components\TextInput::make('eritrosit'),
                                        Forms\Components\TextInput::make('mcv'),
                                        Forms\Components\TextInput::make('mch'),
                                        Forms\Components\TextInput::make('mchc'),
                                        Forms\Components\TextInput::make('rdw'),
                                        Forms\Components\TextInput::make('led'),
                                    ]),
                                    Fieldset::make('Hitung Jenis Leukosit')->columns(2)->schema([
                                        Forms\Components\TextInput::make('eosinofil'),
                                        Forms\Components\TextInput::make('basofil'),
                                        Forms\Components\TextInput::make('netrofil_batang'),
                                        Forms\Components\TextInput::make('netrofil_segmen'),
                                        Forms\Components\TextInput::make('limfosit'),
                                        Forms\Components\TextInput::make('monosit'),
                                    ])
                                ]),
                            ]),
                            // Kolom Kanan
                            Forms\Components\Group::make()->schema([
                                Section::make('Urinalisa')->schema([
                                    Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('urine_warna'),
                                        Forms\Components\TextInput::make('urine_kejernihan'),
                                        Forms\Components\TextInput::make('urine_berat_jenis'),
                                        Forms\Components\TextInput::make('urine_ph'),
                                        Forms\Components\TextInput::make('urine_protein')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_glukosa')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_keton')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_darah')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_bilirubin')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_urobilinogen'),
                                        Forms\Components\TextInput::make('urine_nitrit')->default('Negatif'),
                                        Forms\Components\TextInput::make('urine_leukosit_esterase')->default('Negatif'),
                                    ]),
                                    Fieldset::make('Sedimen')->columns(3)->schema([
                                        Forms\Components\TextInput::make('sedimen_leukosit'),
                                        Forms\Components\TextInput::make('sedimen_eritrosit'),
                                        Forms\Components\TextInput::make('sedimen_silinder')->default('Negatif'),
                                        Forms\Components\TextInput::make('sedimen_sel_epitel'),
                                        Forms\Components\TextInput::make('sedimen_kristal')->default('Negatif'),
                                        Forms\Components\TextInput::make('sedimen_bakteria')->default('Negatif'),
                                        Forms\Components\TextInput::make('sedimen_lain_lain'),
                                    ])
                                ]),
                            ]),
                        ])
                    ]),
                    Tab::make('Kimia Klinik')->schema([
                        Fieldset::make('Glukosa')->schema([
                            Forms\Components\TextInput::make('glukosa_puasa'),
                            Forms\Components\TextInput::make('glukosa_2_jam_pp'),
                        ]),
                        Grid::make(2)->schema([
                            Fieldset::make('Fungsi Ginjal')->schema([
                                Forms\Components\TextInput::make('ureum'),
                                Forms\Components\TextInput::make('kreatinin'),
                                Forms\Components\TextInput::make('asam_urat'),
                                Forms\Components\TextInput::make('hbeag'),
                            ]),
                            Fieldset::make('Fungsi Hati')->schema([
                                Forms\Components\TextInput::make('sgot'),
                                Forms\Components\TextInput::make('sgpt'),
                                Forms\Components\TextInput::make('alkali_fosfatase'),
                                Forms\Components\TextInput::make('kolinesterase'),
                                Forms\Components\TextInput::make('bilirubin_total'),
                                Forms\Components\TextInput::make('bilirubin_direk'),
                                Forms\Components\TextInput::make('bilirubin_indirek'),
                            ]),
                        ]),
                        Fieldset::make('Profil Lemak')->schema([
                            Forms\Components\TextInput::make('kolesterol_total'),
                            Forms\Components\TextInput::make('hdl'),
                            Forms\Components\TextInput::make('ldl'),
                            Forms\Components\TextInput::make('trigliserida'),
                            Forms\Components\TextInput::make('hba1c'),
                        ]),
                    ]),
                    Tab::make('Serologi, Imunologi & Narkoba')->schema([
                        Grid::make(2)->schema([
                            Section::make('Serologi & Imunologi')->schema([
                                Forms\Components\TextInput::make('tpha'),
                                Forms\Components\TextInput::make('vdrl'),
                                Forms\Components\TextInput::make('hbsag'),
                                Forms\Components\TextInput::make('anti_hcv'),
                                Forms\Components\TextInput::make('anti_hbs'),
                            ]),
                            Section::make('Skrining Narkoba')->schema([
                                Forms\Components\TextInput::make('narkoba_amphetamine')->default('Negatif'),
                                Forms\Components\TextInput::make('narkoba_thc')->default('Negatif'),
                                Forms\Components\TextInput::make('narkoba_morphine')->default('Negatif'),
                                Forms\Components\TextInput::make('narkoba_benzodiazepine')->default('Negatif'),
                                Forms\Components\TextInput::make('narkoba_methamphetamine')->default('Negatif'),
                                Forms\Components\TextInput::make('narkoba_cocaine')->default('Negatif'),
                                Forms\Components\TextInput::make('alkohol_urin')->default('Negatif'),
                            ]),
                        ]),
                    ]),
                ])->columnSpanFull(),

                Section::make('Penanggung Jawab')->schema([
                    Forms\Components\TextInput::make('penanggung_jawab')->default('dr. Ridla Ubaidillah, Sp.PK'),
                    Forms\Components\FileUpload::make('tanda_tangan')->label('Upload TTD')->image()->disk('public')->directory('ttd-lab'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_lab')->searchable(),
                Tables\Columns\TextColumn::make('participant.name')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pemeriksaan')->date()->sortable(),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(LabCheck $record): string => route('lab.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLabChecks::route('/'),
            'create' => Pages\CreateLabCheck::route('/create'),
            'edit' => Pages\EditLabCheck::route('/{record}/edit'),
        ];
    }
}
