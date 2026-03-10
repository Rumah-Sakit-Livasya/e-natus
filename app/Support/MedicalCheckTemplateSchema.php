<?php

namespace App\Support;

use Illuminate\Support\Str;

class MedicalCheckTemplateSchema
{
    /**
     * @var array<int, string>
     */
    private const BASE_COLUMNS = [
        'id',
        'project_request_id',
        'nama_proyek',
        'participant_id',
        'participant_name',
        'employee_code',
        'date_of_birth',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const TYPE_COLUMN_ORDER = [
        'audiometry' => [
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'ad_ac_250',
            'ad_ac_500',
            'ad_ac_1000',
            'ad_ac_2000',
            'ad_ac_3000',
            'ad_ac_4000',
            'ad_ac_6000',
            'ad_ac_8000',
            'as_ac_250',
            'as_ac_500',
            'as_ac_1000',
            'as_ac_2000',
            'as_ac_3000',
            'as_ac_4000',
            'as_ac_6000',
            'as_ac_8000',
            'ad_bc_250',
            'ad_bc_500',
            'ad_bc_1000',
            'ad_bc_2000',
            'ad_bc_3000',
            'ad_bc_4000',
            'ad_bc_6000',
            'ad_bc_8000',
            'as_bc_250',
            'as_bc_500',
            'as_bc_1000',
            'as_bc_2000',
            'as_bc_3000',
            'as_bc_4000',
            'as_bc_6000',
            'as_bc_8000',
            'derajat_ad',
            'derajat_as',
            'derajat_ad_bc',
            'derajat_as_bc',
            'kesimpulan',
            'saran',
            'dokter_id',
            'tanda_tangan',
        ],
        'drug_test' => [
            'no_mcu',
            'department',
            'tanggal_pemeriksaan',
            'amphetamine',
            'metamphetamine',
            'cocaine',
            'thc',
            'morphine',
            'benzodiazepine',
            'analis_kesehatan',
        ],
        'ekg' => [
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'irama',
            'heart_rate',
            'axis',
            'pr_interval',
            'qrs_duration',
            'gel_t',
            'st_t_changes',
            'kelainan',
            'kesimpulan',
            'dokter_id',
            'dokter_pemeriksa',
            'tanda_tangan',
            'gambar_hasil_ekg',
        ],
        'lab' => [
            'no_rm',
            'no_lab',
            'instansi',
            'tanggal_pemeriksaan',
            'hemoglobin',
            'leukosit',
            'trombosit',
            'hematokrit',
            'eritrosit',
            'mcv',
            'mch',
            'mchc',
            'rdw',
            'led',
            'eosinofil',
            'basofil',
            'netrofil_batang',
            'netrofil_segmen',
            'limfosit',
            'monosit',
            'urine_warna',
            'urine_kejernihan',
            'urine_berat_jenis',
            'urine_ph',
            'urine_protein',
            'urine_glukosa',
            'urine_keton',
            'urine_darah',
            'urine_bilirubin',
            'urine_urobilinogen',
            'urine_nitrit',
            'urine_leukosit_esterase',
            'sedimen_leukosit',
            'sedimen_eritrosit',
            'sedimen_silinder',
            'sedimen_sel_epitel',
            'sedimen_kristal',
            'sedimen_bakteria',
            'sedimen_lain_lain',
            'glukosa_puasa',
            'glukosa_2_jam_pp',
            'ureum',
            'kreatinin',
            'asam_urat',
            'hbeag',
            'sgot',
            'sgpt',
            'alkali_fosfatase',
            'kolinesterase',
            'bilirubin_total',
            'bilirubin_direk',
            'bilirubin_indirek',
            'kolesterol_total',
            'hdl',
            'ldl',
            'trigliserida',
            'hba1c',
            'tpha',
            'vdrl',
            'hbsag',
            'anti_hcv',
            'anti_hbs',
            'narkoba_amphetamine',
            'narkoba_thc',
            'narkoba_morphine',
            'narkoba_benzodiazepine',
            'narkoba_methamphetamine',
            'narkoba_cocaine',
            'alkohol_urin',
            'dokter_id',
            'penanggung_jawab',
            'tanda_tangan',
        ],
        'rontgen' => [
            'no_rontgen',
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'temuan',
            'kesan',
            'dokter_id',
            'radiologist',
            'tanda_tangan',
            'gambar_hasil_rontgen',
        ],
        'spirometry' => [
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'vc_nilai',
            'vc_prediksi',
            'vc_percent',
            'fvc_nilai',
            'fvc_prediksi',
            'fvc_percent',
            'fev1_nilai',
            'fev1_prediksi',
            'fev1_percent',
            'fev1_fvc_nilai',
            'fev1_fvc_prediksi',
            'fev1_fvc_percent',
            'kesan',
            'saran',
            'dokter_id',
            'dokter_pemeriksa',
            'tanda_tangan',
            'gambar_hasil_spirometri',
        ],
        'treadmill' => [
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'metode',
            'ekg_resting',
            'ekg_exercise_st_change',
            'ekg_exercise_aritmia',
            'td_awal',
            'td_tertinggi',
            'indikasi_berhenti',
            'target_hr',
            'tercapai_hr',
            'lama_tes_menit',
            'lama_tes_detik',
            'kapasitas_aerobik',
            'kelas_fungsional',
            'tingkat_kebugaran',
            'kesimpulan',
            'saran',
            'dokter_id',
            'cardiologist',
            'tanda_tangan',
            'gambar_hasil_treadmill',
        ],
        'usg_abdomen' => [
            'no_rm',
            'instansi',
            'tanggal_pemeriksaan',
            'hepar',
            'gallbladder',
            'lien',
            'pankreas',
            'ren_kanan',
            'ren_kiri',
            'vesica_urinaria',
            'prostat',
            'catatan_tambahan_1',
            'catatan_tambahan_2',
            'kesimpulan',
            'dokter_id',
            'radiologist',
            'tanda_tangan',
            'gambar_hasil_usg',
            'gambar_hasil_usg_2',
            'gambar_hasil_usg_3',
            'gambar_hasil_usg_4',
        ],
        'usg_mammae' => [
            'no_rm',
            'instansi',
            'nik_no_pekerja',
            'tanggal_pemeriksaan',
            'mammae_kanan',
            'mammae_kiri',
            'catatan_tambahan',
            'kesimpulan',
            'dokter_id',
            'radiologist',
            'tanda_tangan',
            'gambar_hasil_usg',
            'gambar_hasil_usg_2',
            'gambar_hasil_usg_3',
            'gambar_hasil_usg_4',
            'gambar_hasil_usg_5',
            'gambar_hasil_usg_6',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const BASE_LABELS = [
        'id' => 'ID',
        'project_request_id' => 'Project Request ID',
        'nama_proyek' => 'Nama Proyek',
        'participant_id' => 'Participant ID',
        'participant_name' => 'Nama Peserta',
        'employee_code' => 'No. NIK',
        'date_of_birth' => 'Tanggal Lahir',
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const TYPE_LABELS = [
        'audiometry' => [
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Tanggal Pelaksanaan',
            'ad_ac_250' => 'AD AC 250 Hz',
            'ad_ac_500' => 'AD AC 500 Hz',
            'ad_ac_1000' => 'AD AC 1000 Hz',
            'ad_ac_2000' => 'AD AC 2000 Hz',
            'ad_ac_3000' => 'AD AC 3000 Hz',
            'ad_ac_4000' => 'AD AC 4000 Hz',
            'ad_ac_6000' => 'AD AC 6000 Hz',
            'ad_ac_8000' => 'AD AC 8000 Hz',
            'as_ac_250' => 'AS AC 250 Hz',
            'as_ac_500' => 'AS AC 500 Hz',
            'as_ac_1000' => 'AS AC 1000 Hz',
            'as_ac_2000' => 'AS AC 2000 Hz',
            'as_ac_3000' => 'AS AC 3000 Hz',
            'as_ac_4000' => 'AS AC 4000 Hz',
            'as_ac_6000' => 'AS AC 6000 Hz',
            'as_ac_8000' => 'AS AC 8000 Hz',
            'ad_bc_250' => 'AD BC 250 Hz',
            'ad_bc_500' => 'AD BC 500 Hz',
            'ad_bc_1000' => 'AD BC 1000 Hz',
            'ad_bc_2000' => 'AD BC 2000 Hz',
            'ad_bc_3000' => 'AD BC 3000 Hz',
            'ad_bc_4000' => 'AD BC 4000 Hz',
            'ad_bc_6000' => 'AD BC 6000 Hz',
            'ad_bc_8000' => 'AD BC 8000 Hz',
            'as_bc_250' => 'AS BC 250 Hz',
            'as_bc_500' => 'AS BC 500 Hz',
            'as_bc_1000' => 'AS BC 1000 Hz',
            'as_bc_2000' => 'AS BC 2000 Hz',
            'as_bc_3000' => 'AS BC 3000 Hz',
            'as_bc_4000' => 'AS BC 4000 Hz',
            'as_bc_6000' => 'AS BC 6000 Hz',
            'as_bc_8000' => 'AS BC 8000 Hz',
            'derajat_ad' => 'Derajat Ambang Dengar Kanan (AD)',
            'derajat_as' => 'Derajat Ambang Dengar Kiri (AS)',
            'derajat_ad_bc' => 'Derajat Ambang Dengar Kanan (AD) - Bone Conduction',
            'derajat_as_bc' => 'Derajat Ambang Dengar Kiri (AS) - Bone Conduction',
            'dokter_id' => 'Dokter Pemeriksa',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
        ],
        'drug_test' => [
            'no_mcu' => 'No. MCU',
            'department' => 'Instansi',
            'analis_kesehatan' => 'Analis Kesehatan',
        ],
        'ekg' => [
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'pr_interval' => 'PR Interval',
            'qrs_duration' => 'QRS Duration',
            'gel_t' => 'Gel T',
            'st_t_changes' => 'ST-T Changes',
            'dokter_id' => 'Dokter (Cardiologist)',
            'dokter_pemeriksa' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_ekg' => 'Upload Gambar Hasil EKG',
        ],
        'lab' => [
            'no_rm' => 'No. RM',
            'no_lab' => 'No. Lab',
            'tanggal_pemeriksaan' => 'Tanggal Pemeriksaan',
            'urine_ph' => 'Urine pH',
            'hba1c' => 'HbA1c',
            'dokter_id' => 'Dokter Penanggung Jawab',
            'penanggung_jawab' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
        ],
        'rontgen' => [
            'no_rontgen' => 'No. Rontgen',
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'temuan' => 'Yth, TS.',
            'dokter_id' => 'Radiologist',
            'radiologist' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_rontgen' => 'Upload Gambar Hasil Rontgen',
        ],
        'spirometry' => [
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'vc_nilai' => 'Best VC (Nilai)',
            'vc_prediksi' => 'Best VC (Prediksi)',
            'vc_percent' => 'Best VC (%)',
            'fvc_nilai' => 'Best FVC (Nilai)',
            'fvc_prediksi' => 'Best FVC (Prediksi)',
            'fvc_percent' => 'Best FVC (%)',
            'fev1_nilai' => 'Best FEV1 (Nilai)',
            'fev1_prediksi' => 'Best FEV1 (Prediksi)',
            'fev1_percent' => 'Best FEV1 (%)',
            'fev1_fvc_nilai' => 'FEV1 / FVC (Nilai)',
            'fev1_fvc_prediksi' => 'FEV1 / FVC (Prediksi)',
            'fev1_fvc_percent' => 'FEV1 / FVC (%)',
            'dokter_id' => 'Dokter Pemeriksa',
            'dokter_pemeriksa' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_spirometri' => 'Upload Gambar Hasil Spirometri',
        ],
        'treadmill' => [
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'ekg_resting' => 'EKG Resting',
            'ekg_exercise_st_change' => 'ST-T Change Segmen',
            'ekg_exercise_aritmia' => 'Aritmia',
            'td_awal' => 'Awal',
            'td_tertinggi' => 'Tertinggi',
            'target_hr' => 'Target HR',
            'tercapai_hr' => 'Tercapai HR',
            'lama_tes_menit' => 'Menit',
            'lama_tes_detik' => 'Detik',
            'dokter_id' => 'Cardiologist',
            'cardiologist' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_treadmill' => 'Upload Lampiran Hasil Treadmill (PDF)',
        ],
        'usg_abdomen' => [
            'no_rm' => 'No. RM',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'catatan_tambahan_1' => 'Catatan Tambahan Baris 1',
            'catatan_tambahan_2' => 'Catatan Tambahan Baris 2',
            'dokter_id' => 'Radiologist',
            'radiologist' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_usg' => 'Gambar Hasil USG 1',
            'gambar_hasil_usg_2' => 'Gambar Hasil USG 2 (Opsional)',
            'gambar_hasil_usg_3' => 'Gambar Hasil USG 3 (Opsional)',
            'gambar_hasil_usg_4' => 'Gambar Hasil USG 4 (Opsional)',
        ],
        'usg_mammae' => [
            'no_rm' => 'No. RM',
            'nik_no_pekerja' => 'NIK/No. Pekerja',
            'tanggal_pemeriksaan' => 'Pelaksanaan',
            'mammae_kanan' => 'Mammae Kanan',
            'mammae_kiri' => 'Mammae Kiri',
            'catatan_tambahan' => 'Catatan Tambahan',
            'dokter_id' => 'Radiologist',
            'radiologist' => 'Nama Dokter',
            'tanda_tangan' => 'Tanda Tangan (Auto dari Dokter)',
            'gambar_hasil_usg' => 'Gambar Hasil USG 1',
            'gambar_hasil_usg_2' => 'Gambar Hasil USG 2 (Opsional)',
            'gambar_hasil_usg_3' => 'Gambar Hasil USG 3 (Opsional)',
            'gambar_hasil_usg_4' => 'Gambar Hasil USG 4 (Opsional)',
            'gambar_hasil_usg_5' => 'Gambar Hasil USG 5 (Opsional)',
            'gambar_hasil_usg_6' => 'Gambar Hasil USG 6 (Opsional)',
        ],
    ];

    /**
     * @param array<int, string> $dbColumns
     * @return array<int, string>
     */
    public static function orderedColumns(string $type, array $dbColumns): array
    {
        $checkColumns = array_values(array_filter(
            array_diff($dbColumns, ['id', 'participant_id']),
            fn(string $column): bool => self::isExcelInputColumn($column)
        ));
        $orderedPreferred = self::TYPE_COLUMN_ORDER[$type] ?? [];
        $orderedCheckColumns = array_values(array_intersect($orderedPreferred, $checkColumns));
        $remainingColumns = array_values(array_diff($checkColumns, $orderedCheckColumns));

        return array_values(array_unique(array_merge(
            self::BASE_COLUMNS,
            $orderedCheckColumns,
            $remainingColumns
        )));
    }

    /**
     * @param array<int, string> $columns
     * @return array<string, string>
     */
    public static function labels(string $type, array $columns): array
    {
        $labels = [];
        $typeLabels = self::TYPE_LABELS[$type] ?? [];

        foreach ($columns as $column) {
            $labels[$column] = $typeLabels[$column]
                ?? self::BASE_LABELS[$column]
                ?? self::defaultLabel($column);
        }

        return $labels;
    }

    /**
     * @return array<string, string>
     */
    public static function headingAliasMap(string $type): array
    {
        $aliases = [];
        $labels = array_merge(self::BASE_LABELS, self::TYPE_LABELS[$type] ?? []);

        foreach ($labels as $column => $label) {
            $normalized = self::normalizeHeading($label);
            if ($normalized !== '' && $normalized !== $column) {
                $aliases[$normalized] = $column;
            }
        }

        return $aliases;
    }

    private static function defaultLabel(string $column): string
    {
        $label = Str::of($column)->replace('_', ' ')->title()->toString();

        $label = str_replace(
            [' Ekg ', ' Usg ', ' Mcu ', ' Td ', ' Sgot', 'Sgpt', ' Hdl', ' Ldl', ' Hba1C '],
            [' EKG ', ' USG ', ' MCU ', ' TD ', ' SGOT', 'SGPT', ' HDL', ' LDL', ' HbA1c '],
            " {$label} "
        );

        return trim(preg_replace('/\s+/', ' ', $label) ?? $label);
    }

    public static function isExcelInputColumn(string $column): bool
    {
        return ! Str::startsWith($column, 'gambar_hasil_');
    }

    public static function normalizeHeading(string $heading): string
    {
        $heading = strtolower(trim($heading));
        $heading = preg_replace('/[^a-z0-9]+/', '_', $heading) ?? '';
        $heading = trim($heading, '_');

        return preg_replace('/_+/', '_', $heading) ?? '';
    }
}
