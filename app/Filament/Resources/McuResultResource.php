<?php

namespace App\Filament\Resources;

use App\Filament\Resources\McuResultResource\Pages;
use App\Filament\Resources\McuResultResource\RelationManagers;
use App\Models\McuResult;
use Filament\Forms\Get;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class McuResultResource extends Resource
{
    protected static ?string $model = McuResult::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Project';
    protected static ?string $label = 'MCU Result';
    protected static ?string $pluralLabel = 'MCU Results';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informasi Dasar')
                        ->schema([
                            Forms\Components\Select::make('project_request_id')->options(\App\Models\ProjectRequest::pluck('name', 'id'))->searchable()->preload()->required()->label('Proyek MCU')->live(),
                            Forms\Components\Select::make('participant_id')->required()->label('Peserta')->searchable()->options(function (Get $get) {
                                $projectId = $get('project_request_id');
                                if ($projectId) {
                                    return \App\Models\Participant::where('project_request_id', $projectId)->pluck('name', 'id');
                                }
                                return [];
                            }),
                            Forms\Components\TextInput::make('no_mcu')->required()->label('No. MCU'),
                            Forms\Components\DatePicker::make('tanggal_mcu')->required()->label('Tanggal MCU'),
                        ]),

                    Forms\Components\Wizard\Step::make('Riwayat Penyakit & Gaya Hidup')
                        ->schema([
                            Forms\Components\Section::make('Riwayat Penyakit dan Gaya Hidup')->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_penyakit_saat_ini')->label('Riwayat Penyakit Saat Ini')->default('Tidak ada'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_penyakit_dahulu')->label('Riwayat Penyakit Dahulu')->default('Riwayat Gastritis'),
                                    Forms\Components\Textarea::make('riwayat_penyakit_dan_gaya_hidup.riwayat_penyakit_keluarga')->label('Riwayat Penyakit Keluarga')->default('Bapak riwayat HT DM, Ibu riwayat hipotensi, Saudara kandung DM')->rows(3),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_kecelakaan')->label('Riwayat Kecelakaan')->default('Tidak ada'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_vaksinasi')->label('Riwayat Vaksinasi')->default('Booster'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_obsgyn')->label('Riwayat Obsgyn')->default('Tidak ada'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.riwayat_operasi')->label('Riwayat Operasi')->default('Op APP tahun 2017'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.keluhan_umum')->label('Keluhan Umum')->default('Badan terasa capek'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.konsumsi_buah_sayur')->label('Konsumsi Buah/Sayur')->default('Cukup'),
                                ])->columnSpan(1),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.keluhan_dgn_pekerjaan')->label('Keluhan Dgn Pekerjaan')->default('Tidak ada'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.olah_raga')->label('Olah Raga')->default('Tidak rutin, jalan sehat'),
                                    Forms\Components\Textarea::make('riwayat_penyakit_dan_gaya_hidup.alergi')->label('Alergi')->default('Debu, asam mefenamat, ketorolac, penicilin, catapres')->rows(3),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.diet')->label('Diet')->default('Tidak'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.kopi')->label('Kopi')->default('Ya, 1 gelas. hari'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.merokok')->label('Merokok')->default('Tidak'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.obat_rutin')->label('Obat Rutin')->default('Tidak ada'),
                                    Forms\Components\TextInput::make('riwayat_penyakit_dan_gaya_hidup.alkohol')->label('Alkohol')->default('Tidak'),
                                ])->columnSpan(1),
                            ])->columns(2),
                        ]),

                    Forms\Components\Wizard\Step::make('Pemeriksaan Fisik')
                        ->schema([
                            Forms\Components\Section::make('Hazard')->schema([
                                Forms\Components\TextInput::make('anamnesa.biological_monitoring')->label('Biological Monitoring')->default('Tidak dilakukan'),
                                Forms\Components\TextInput::make('anamnesa.hazard')->label('Hazard')->default('Duduk 7 jam sehari, radiasi layar komputer'),
                            ]),
                            Forms\Components\Section::make('Hasil Pemeriksaan Vital Sign')->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.berat_badan')->label('Berat Badan')->numeric()->default(82)->suffix('Kg'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.tinggi_badan')->label('Tinggi Badan')->numeric()->default(165)->suffix('cm'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.ratio_imt_bmi')->label('Ratio IMT / BMI')->default('30,10')->suffix('Kg/m²'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.status_gizi')->label('Status Gizi')->default('Obese'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.lingkar_perut')->label('Lingkar Perut')->numeric()->default(98)->suffix('cm'),
                                    ]),
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.td_sistole')->label('TD Sistole')->numeric()->default(140)->suffix('mm Hg'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.td_diastole')->label('TD Diastole')->numeric()->default(90)->suffix('mm Hg'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.nadi')->label('Nadi')->numeric()->default(70)->suffix('/menit'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.suhu_badan')->label('Suhu Badan')->default('36,1')->suffix('°C'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_vital_sign.pernafasan')->label('Pernafasan')->numeric()->default(22)->suffix('/menit'),
                                    ]),
                                ])
                            ]),
                            Forms\Components\Section::make('Hasil Pemeriksaan Fisik Dokter MCU')->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.kepala')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.leher')->default('Normal'),
                                        Forms\Components\Section::make('Mata')->schema([
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.avod')->label('AVOD')->default('6/7 ; Plano'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.avos')->label('AVOS')->default('6/7; C-0,50 X 100 ; 6/6'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.add')->label('ADD')->default('+2,25'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.penglihatan')->label('Penglihatan')->default('Normal'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.lapang_pandang')->label('Lapang Pandang')->default('Normal'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.tekanan_bola_mata')->label('Tekanan Bola Mata')->default('NCT 18/0'),
                                            Forms\Components\Textarea::make('hasil_pemeriksaan_fisik_dokter.mata.saran_mata')->label('Saran Mata')->rows(2)->default('Tidak ada'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mata.kesimpulan_mata')->label('Kesimpulan Mata')->default('Normal'),
                                        ]),
                                        Forms\Components\Section::make('THT')->schema([
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tht.telinga')->label('Telinga')->default('ADS Cerume Impacted'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tht.hidung')->label('Hidung')->default('Normal'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tht.tenggorokan')->label('Tenggorokan')->default('Normal'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tht.anamnesa_tht')->label('Anamnesa THT')->default('Tidak ada keluhan'),
                                            Forms\Components\Textarea::make('hasil_pemeriksaan_fisik_dokter.tht.saran_tht')->label('Saran THT')->rows(2)->default('Konsultasi dengan dokter Spesialis THT-KL terkait ADS Cerumen Impacted'),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tht.kesimpulan_tht')->label('Kesimpulan THT')->default('ADS Cerume Impacted'),
                                        ]),
                                        Forms\Components\Section::make('Gigi dan Mulut')->schema([
                                            Forms\Components\Grid::make(3)->schema([
                                                Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.gigi.d')->label('D')->default('5'),
                                                Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.gigi.m')->label('M')->default('0'),
                                                Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.gigi.f')->label('F')->default('4'),
                                            ]),
                                            Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.gigi.advice')->label('Advice Gigi dan Mulut')->default('Pro scalling'),
                                        ]),
                                    ]),
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.kesadaran')->default('Compos mentis'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.thorax')->label('Thorax : Jantung Paru')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.abdomen')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.hati_hepar')->label('Hati/Hepar')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.limpa_lien')->label('Limpa/Lien')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.ginjal')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.extermitas')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.reflex_fisiologis')->label('Reflex Fisiologis')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.reflex_patologis')->label('Reflex Patologis')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.haemorhoid')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.kulit')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.irama_nadi')->label('Irama Nadi')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.funduscopy')->default('Tidak Dilakukan'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.buta_warna')->label('Buta Warna')->default('Tidak'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.mulut')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tonsil')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.pharynx')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.tulang_belakang')->label('Tulang Belakang')->default('Normal'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.lain_lain')->label('Lain - Lain')->default('Romberg Negatif'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.dokter_pemeriksa')->label('Dokter Pemeriksa')->default('dr. Mohammad Andy Suryawijaya'),
                                        Forms\Components\TextInput::make('hasil_pemeriksaan_fisik_dokter.sip_dokter')->label('SIP')->default('446/0531/SIPdokter/DPMPTSP/SIMPOK/IV/2024'),
                                    ]),
                                ])
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Hasil Laboratorium')
                        ->schema([
                            // Konten hasil lab dari langkah sebelumnya...
                            Forms\Components\Section::make('A. Hasil Pemeriksaan Darah Lengkap')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.hemoglobin')->label('Hemoglobin')->default('15,2')->suffix('g/dl')->helperText('Rujukan: [12.0-18.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.hematokrit')->label('Hematokrit')->default('42,9')->suffix('vol.%')->helperText('Rujukan: [36.0-54.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.lekosit')->label('Lekosit')->default('6,8')->suffix('ribu/uL')->helperText('Rujukan: [5.00-10.00]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.trombosit')->label('Trombosit')->default('332')->suffix('ribu/uL')->helperText('Rujukan: [150-450]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.eritrosit')->label('Eritrosit')->default('4,6')->suffix('juta/uL')->helperText('Rujukan: [4-6]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.mcv')->label('MCV')->default('-')->suffix('/um')->helperText('Rujukan: [80.0-94.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.mch')->label('MCH')->default('-')->suffix('pg')->helperText('Rujukan: [26.0-32.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.mchc')->label('MCHC')->default('-')->suffix('g/dl')->helperText('Rujukan: [32.0-37.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.led_i')->label('LED I')->default('6')->suffix('mm/jam')->helperText('Rujukan: [0-10]'),
                                        ]),
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.basofil')->label('Basofil')->default('0')->suffix('%')->helperText('Rujukan: [0.0-1.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.eosinofil')->label('Eosinofil')->default('2')->suffix('%')->helperText('Rujukan: [1.0-3.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.batang')->label('Batang')->default('3')->suffix('%')->helperText('Rujukan: [2-6]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.segmen')->label('Segmen')->default('61')->suffix('%')->helperText('Rujukan: [40.0-60.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.limposit')->label('Limposit')->default('27')->suffix('%')->helperText('Rujukan: [20.0-45.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.monosit')->label('Monosit')->default('7')->suffix('%')->helperText('Rujukan: [0.0-1.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.darah_lengkap.hapusan_darah')->label('Hapusan Darah Morfology')->default('-'),
                                        ]),
                                    ]),
                                ]),
                            Forms\Components\Section::make('B. Hasil Pemeriksaan Faal HATI, Lemak dan Ginjal')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.bill_total')->label('Bill.Total')->default('0,89')->suffix('mg/dl')->helperText('Rujukan: [0.20-1.20]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.sgot')->label('SGOT')->default('25')->suffix('u/l')->helperText('Rujukan: [ < = 41]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.sgpt')->label('SGPT')->default('28')->suffix('u/l')->helperText('Rujukan: [ < = 40]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.alkali_fotspat')->label('Alkali Fotspat')->default('81')->suffix('U/I')->helperText('Rujukan: [40 - 129]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.glukosa_puasa')->label('Glukosa Puasa')->default('97')->suffix('mg/dl')->helperText('Rujukan: [70.0-105.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.glukosa_2jam_pp')->label('Glukosa 2Jam PP')->default('-')->suffix('mg/dl')->helperText('Rujukan: [70.0-139.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.bill_direct')->label('Bill Direct')->default('-')->suffix('mg/dl')->helperText('Rujukan: [0.60]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.faal_hati.bill_indirect')->label('Bill Indirect')->default('-')->suffix('mg/dl')->helperText('Rujukan: [0.60]'),
                                        ]),
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.hba1c')->label('HbA1C')->default('5,6')->suffix('%')->helperText('Rujukan: [4.6-6.6]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.cholest_total')->label('Cholest Total')->default('155')->suffix('mg/dl')->helperText('Rujukan: [0-200]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.triglyceride')->label('Triglyceride')->default('90')->suffix('mg/dl')->helperText('Rujukan: [0-150]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.hdl_cholest')->label('HDL Cholest')->default('48')->suffix('mg/dl')->helperText('Rujukan: [40-60]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.ldl_cholest')->label('LDL Cholest')->default('118')->suffix('mg/dl')->helperText('Rujukan: [0-100]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.ureum')->label('Ureum')->default('25')->suffix('mg/dl')->helperText('Rujukan: [10.0-50.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.kreatinin')->label('Kreatinin')->default('0,98')->suffix('mg/dl')->helperText('Rujukan: [0.67-1.17]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.lemak_ginjal.asam_urat')->label('Asam Urat')->default('5,1')->suffix('mg/dl')->helperText('Rujukan: [3.40-7.00]'),
                                        ]),
                                    ]),
                                ]),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Section::make('C. Hasil Pemeriksaan Drug Test')
                                    ->schema([
                                        Forms\Components\TextInput::make('hasil_laboratorium.drug_test.amphet')->label('amphet')->default('-')->helperText('Rujukan: [Negatif]'),
                                        Forms\Components\TextInput::make('hasil_laboratorium.drug_test.meth_amphet')->label('Meth-amphet')->default('-')->helperText('Rujukan: [Negatif]'),
                                        Forms\Components\TextInput::make('hasil_laboratorium.drug_test.marijuana')->label('Marijuana')->default('-')->helperText('Rujukan: [Negatif]'),
                                        Forms\Components\TextInput::make('hasil_laboratorium.drug_test.morfin')->label('Morfin')->default('-')->helperText('Rujukan: [Negatif]'),
                                        Forms\Components\TextInput::make('hasil_laboratorium.drug_test.benzodiazepin')->label('Benzodiazepin')->default('-')->helperText('Rujukan: [Negatif]'),
                                    ]),
                                Forms\Components\Section::make('D. Hasil Pemeriksaan Immunoserologi')
                                    ->schema([
                                        Forms\Components\TextInput::make('hasil_laboratorium.immunoserologi.hbsag')->label('HbsAg')->default('NON REAKTIF')->helperText('Rujukan: [Non Reaktif]'),
                                        Forms\Components\TextInput::make('hasil_laboratorium.immunoserologi.anti_hbs')->label('Anti HBs')->default('NON REAKTIF')->helperText('Rujukan: [Non Reaktif]'),
                                    ]),
                            ]),
                            Forms\Components\Section::make('E. Hasil Pemeriksaan Urin')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.protein_urin')->label('Protein Urin')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.reduksi_n')->label('Reduksi N')->default('NEGATIF')->helperText('Rujukan: [Normal]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.reduksi_pp')->label('Reduksi PP')->default('-')->helperText('Rujukan: [Normal]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.bilirubin')->label('Bilirubin')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.urobilinogen')->label('Urobilinogen')->default('NEGATIF')->helperText('Rujukan: [Normal]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.leukosit')->label('Leukosit')->default('1-2')->helperText('Rujukan: [1-4]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.eritrosit')->label('Eritrosit')->default('0-1')->helperText('Rujukan: [0-1]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.berat_jenis')->label('Berat Jenis')->default('1.010')->helperText('Rujukan: [1.015-1.025]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.nitrit')->label('Nitrit')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.aseton_urin')->label('Aseton Urin')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                        ]),
                                        Forms\Components\Grid::make(1)->schema([
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.sel_epitel')->label('Sel Epitel')->default('0-1')->helperText('Rujukan: [5-15]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.kristal')->label('Kristal')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.jamur')->label('Jamur')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.silinder')->label('Silinder')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.bakteri')->label('Bakteri')->default('NEGATIF')->helperText('Rujukan: [4.5-8.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.reaksi')->label('Reaksi')->default('-')->helperText('Rujukan: [4.5-8.0]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.warna_urin')->label('Warna Urin')->default('KUNING JERNIH')->helperText('Rujukan: [Kuning]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.leukosit_esterase')->label('Leukosit Esterase')->default('NEGATIF')->helperText('Rujukan: [Negatif]'),
                                            Forms\Components\TextInput::make('hasil_laboratorium.urin.ph_urine')->label('PH Urine')->default('5,0')->helperText('Rujukan: [4.5-8.0]'),
                                        ]),
                                    ]),
                                ]),
                            Forms\Components\TextInput::make('hasil_laboratorium.penanggung_jawab')->label('Penanggung Jawab')->default('ADRIAN SUHENDRA, dr., Sp.PK.'),
                        ]),

                    Forms\Components\Wizard\Step::make('Status Kesehatan')
                        ->schema([
                            Forms\Components\Section::make('A. Tes Kebugaran Jasmani')
                                ->schema([
                                    Forms\Components\TextInput::make('status_kesehatan.kebugaran.treadmill_test')->label('Treadmill Test')->default('Respon Iskemik Negatif'),
                                    Forms\Components\TextInput::make('status_kesehatan.kebugaran.status_kebugaran')->label('Status Kebugaran')->default('Baik / Good'),
                                    Forms\Components\TextInput::make('status_kesehatan.kebugaran.target_nadi')->label('Target Nadi Olah Raga')->default('166 x/Menit'),
                                    Forms\Components\TextInput::make('status_kesehatan.kebugaran.napfa_test')->label('NAPFA Test')->default('Tidak dilakukan'),
                                    Forms\Components\TextInput::make('status_kesehatan.kebugaran.rockport')->label('Rockport')->default('-'),
                                ]),
                            Forms\Components\Section::make('B. Penilaian Risiko Penyakit Jantung Koroner (PJK)')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('status_kesehatan.risiko_pjk.risiko_cardiovasculer_score')->label('Risiko Cardiovasculer')->default('7'),
                                        Forms\Components\TextInput::make('status_kesehatan.risiko_pjk.risiko_cardiovasculer_level')->label('Level Risiko')->default('Risiko Tinggi'),
                                    ]),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Hasil Pemeriksaan Penunjang Lainnya')
                        ->schema([
                            Forms\Components\Section::make('A. Pemeriksaan Radiologi')
                                ->schema([
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.radiologi.foto_thorax')->label('Foto Thorax')->default('Normal'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.radiologi.usg_abdomen')->label('USG Abdomen')->default('Fatty Liver'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.radiologi.usg_mammae')->label('USG Mammae')->default('Tidak dilakukan'),
                                    Forms\Components\Textarea::make('hasil_pemeriksaan_penunjang.radiologi.dokter_penanggung_jawab')->label('Dokter Penanggung Jawab')->rows(2)->default("FITRI LUTFIA, dr, Sp.Rad.\ndr Firdaus, Sp.Rad."),
                                ]),
                            Forms\Components\Section::make('B. Pemeriksaan EKG & Treadmill Test')
                                ->schema([
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.ekg.ekg_resting')->label('EKG Resting')->default('OMI Inferior + RBBB'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.ekg.treadmill_test')->label('Treadmill Test')->default('Respon Iskemik Negatif'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.ekg.dokter_penanggung_jawab')->label('Dokter Penanggung Jawab')->default('dr. Gurpreet Dhillon, Sp.JP'),
                                ]),
                            Forms\Components\Section::make('C. Pemeriksaan Pap Smear')
                                ->schema([
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.pap_smear.hasil')->label('Pap Smear')->default('Tidak dilakukan'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.pap_smear.dokter_penanggung_jawab')->label('Dokter Penanggung Jawab')->default('-'),
                                ]),
                            Forms\Components\Section::make('D. Pemeriksaan Audiometri')
                                ->schema([
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.audiometri.hasil')->label('Audiometri')->default('Normal'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.audiometri.dokter_penanggung_jawab')->label('Dokter Penanggung Jawab')->default('dr. Alexander Nur Ilhami.M.Kes,SP. THT-KL'),
                                ]),
                            Forms\Components\Section::make('E. Pemeriksaan Spirometri')
                                ->schema([
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.spirometri.hasil')->label('Spirometri')->default('Fungsi Paru Normal'),
                                    Forms\Components\TextInput::make('hasil_pemeriksaan_penunjang.spirometri.dokter_penanggung_jawab')->label('Dokter Penanggung Jawab')->default('dr. Temmasonge. Sp.P'),
                                ]),
                        ]),


                    Forms\Components\Wizard\Step::make('Nasehat, Diet, Kesimpulan, Catatan dan Saran')
                        ->schema([
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.nasehat')->label('Nasehat')
                                ->default(
                                    "<ul>
                                    <li>Kontrol dengan dokter Klinik Pekerja/ Keluarga untuk evaluasi dan terapi lebih lanjut.</li>
                                    <li>Gaya hidup sehat (GHS) : olah raga aerobik (Jalan, jogging, bersepeda, renang) teratur 3-4 x/minggu, selama 30 - 45 menit dengan target nadi latihan 132 bpm, tidur cukup.</li>
                                    <li>-</li>
                                </ul>"
                                ),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.diet')->label('Diet')
                                ->default(
                                    "<ol>
                                    <li>Diet rendah garam, kurangi makana asin, vetsin, kecap, dendeng, softdrink dengan soda.</li>
                                    <li>Diet rendah lemak ( kurangi:Jeroan,susu full cream,keju,kuning telor,sea food selain ikan,gorengan, minyak, margarin,santan);</li>
                                    <li>Jaga higiene gigi, sikat gigi 2-3x/hari, kurangi makan manis.</li>
                                </ol>"
                                ),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.kesimpulan')->label('Kesimpulan')
                                ->default(
                                    "<ul>
                                    <li>02. Laik bekerja sesuai posisi dan lokasi saat ini, dengan catatan P5. Ditemukan kelainan medis yang serius, risiko kesehatan tinggi</li>
                                </ul>"
                                ),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.catatan')->label('Catatan')
                                ->default(
                                    "<ol>
                                    <li>Hasil EKG: OMI Inferior + RBBB;</li>
                                    <li>Hipertensi derajat 1 (TD: 140/90 mmHg);</li>
                                    <li>Peningkatan LDL Kolesterol (LDL: 118 mg/dL);</li>
                                    <li>Hasil USG Abdomen: Fatty Liver;</li>
                                    <li>THT: ADS Cerumen Impacted;</li>
                                    <li>Obesitas 1 (BMI: 30,1 kg /m²) dengan Obesitas sentralis (LP: 98 cm); Resiko Kardiovaskuler Berat;</li>
                                    <li>Pemeriksaan gigi (Lubang 5 pro filling ; Tambal 4; calculus + Pro Scalling RA, RB).</li>
                                    <li>-</li>
                                </ol>"
                                ),
                            Forms\Components\RichEditor::make('kesimpulan_dan_saran.saran')->label('Saran')
                                ->default(
                                    "<ol>
                                    <li>Konsultasi dengan dokter Spesialis Jantung terkait Hasil EKG: OMI Inferior + RBBB;</li>
                                    <li>Konsultasi dengan dokter Spesialis Penyakit Dalam terkait Hipertensi derajat 1; Peningkatan LDL Kolesterol; dan Hasil USG Abdomen;</li>
                                    <li>Konsultasi dengan dokter Spesialis THT-KL terkait ADS Cerumen Impacted;</li>
                                    <li>Ikuti program wellness untuk menurunkan berat badan secara bertahap dengan olah raga terukur, pengaturan pola makan dan menghindari kebiasan mengudap/ngemil;</li>
                                    <li>Konsultasi ke dokter gigi untuk perawatan gigi dan dianjurkan ke poli gigi setiap 6 bulan sekali.</li>
                                </ol>"
                                ),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('kesimpulan_dan_saran.dokter_pemeriksa_nama')
                                            ->label('Dokter Pemeriksa')
                                            ->default('dr.Mohammad Alwi,HIMu')
                                            ->placeholder('Nama Dokter'),
                                    ])->columnSpan(1),

                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('kesimpulan_dan_saran.penanggung_jawab_nama')
                                            ->label('Penanggung Jawab MCU dan OH- IH')
                                            ->default('dr. Andrian Purwo S, Sp.Ok, HIMu, OGUK')
                                            ->placeholder('Nama Penanggung Jawab'),
                                        Forms\Components\TextInput::make('kesimpulan_dan_saran.penanggung_jawab_sip')
                                            ->label('SIP')
                                            ->default('SIP:33016/53223/ds/01/449.1/0519/II/2021')
                                            ->placeholder('Nomor SIP'),
                                        Forms\Components\TextInput::make('kesimpulan_dan_saran.penanggung_jawab_oguk')
                                            ->label('OGUK Doctor')
                                            ->default('OGUK Doctor (PIN: OGUK/2021/3009)')
                                            ->placeholder('Nomor OGUK'),
                                    ])->columnSpan(1),
                                ]),
                        ]),

                    Forms\Components\Wizard\Step::make('Pemeriksaan Tambahan')
                        ->schema([
                            Forms\Components\Repeater::make('hasil_pemeriksaan_penunjang.pemeriksaan_dinamis')
                                ->label('Data Pemeriksaan Tambahan')
                                ->addActionLabel('Tambah Pemeriksaan Lain')
                                ->schema([
                                    Forms\Components\TextInput::make('judul')
                                        ->label('Judul Pemeriksaan')
                                        ->required()
                                        ->placeholder('Contoh: Tes Alergi Kulit, Hasil Doppler, dll.'),
                                    Forms\Components\RichEditor::make('isi')
                                        ->label('Hasil Pemeriksaan')
                                        ->required()
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'strike',
                                            'bulletList',
                                            'orderedList',
                                            'undo',
                                            'redo',
                                        ]),

                                    // PERUBAHAN ADA DI SINI
                                    Forms\Components\FileUpload::make('lampiran')
                                        ->label('Lampiran (Bisa lebih dari satu)')
                                        ->disk('public')
                                        ->directory('mcu-dynamic-attachments')
                                        ->preserveFilenames()
                                        ->multiple(), // <-- TAMBAHKAN METODE INI
                                ])
                                ->columns(1)
                                ->grid(1)
                                ->defaultItems(0),
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

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return true; // bypass semua permission cek
        }
        return auth()->user()->can('view mcu result');
    }
}
