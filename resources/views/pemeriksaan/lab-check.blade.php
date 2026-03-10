<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Lab - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    @php
        $pageOneSections = [
            [
                'title' => 'HEMATOLOGI LENGKAP',
                'rows' => [
                    ['Hemoglobin', $record->hemoglobin, '13.5 - 17.5', 'g/dL'],
                    ['Leukosit', $record->leukosit, '4.5 - 11.0', '10³/uL'],
                    ['Trombosit', $record->trombosit, '150 - 450', '10³/uL'],
                    ['Hematokrit', $record->hematokrit, '35.0 - 45.0', '%'],
                    ['Eritrosit', $record->eritrosit, '4.50 - 5.90', '10⁶/uL'],
                    ['MCV', $record->mcv, '80.0 - 96.0', 'fl'],
                    ['MCH', $record->mch, '28.0 - 33.0', 'pg'],
                    ['MCHC', $record->mchc, '33.0 - 36.0', 'g/dL'],
                    ['RDW', $record->rdw, '11.6 - 14.6', '%'],
                    ['LED', $record->led, '0 - 20', 'mm/jam'],
                ],
                'subsections' => [
                    [
                        'title' => 'Hitung Jenis Leukosit',
                        'rows' => [
                            ['Eosinofil', $record->eosinofil, '0 - 4', '%'],
                            ['Basofil', $record->basofil, '0 - 2', '%'],
                            ['Netrofil Batang', $record->netrofil_batang, '2 - 6', '%'],
                            ['Netrofil Segmen', $record->netrofil_segmen, '55 - 80', '%'],
                            ['Limfosit', $record->limfosit, '22 - 44', '%'],
                            ['Monosit', $record->monosit, '0 - 7', '%'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'URINALISA',
                'subsections' => [
                    [
                        'title' => 'Urine Lengkap',
                        'rows' => [
                            ['Warna', $record->urine_warna, 'Kuning', ''],
                            ['Kejernihan', $record->urine_kejernihan, 'Jernih', ''],
                            ['Berat Jenis', $record->urine_berat_jenis, '1.015 - 1.025', ''],
                            ['pH', $record->urine_ph, '4.5 - 8.0', ''],
                            ['Protein (Albumin)', $record->urine_protein, 'Negatif', ''],
                            ['Glukosa', $record->urine_glukosa, 'Normal', ''],
                            ['Keton', $record->urine_keton, 'Negatif', ''],
                            ['Darah / Hb', $record->urine_darah, 'Negatif', ''],
                            ['Bilirubin', $record->urine_bilirubin, 'Negatif', ''],
                            ['Urobilinogen', $record->urine_urobilinogen, '0.2 - 1.0', 'mg/dL'],
                            ['Nitrit', $record->urine_nitrit, 'Negatif', ''],
                            ['Leukosit Esterase', $record->urine_leukosit_esterase, 'Negatif', ''],
                        ],
                    ],
                    [
                        'title' => 'Sedimen',
                        'rows' => [
                            ['Leukosit', $record->sedimen_leukosit, '< 5', '/LPB'],
                            ['Eritrosit', $record->sedimen_eritrosit, '< 3', '/LPB'],
                            ['Silinder', $record->sedimen_silinder, 'Negatif', ''],
                            ['Sel Epitel', $record->sedimen_sel_epitel, '', ''],
                            ['Kristal', $record->sedimen_kristal, 'Negatif', ''],
                            ['Bakteria', $record->sedimen_bakteria, 'Negatif', ''],
                            ['Lain - lain', $record->sedimen_lain_lain, '', ''],
                        ],
                    ],
                ],
            ],
        ];

        $pageTwoSections = [
            [
                'title' => 'KIMIA KLINIK',
                'rows' => [
                    ['Glukosa Puasa', $record->glukosa_puasa, '70 - 110', 'mg/dL'],
                    ['Glukosa 2 Jam Pp', $record->glukosa_2_jam_pp, '80 - 140', 'mg/dL'],
                ],
                'subsections' => [
                    [
                        'title' => 'Fungsi Ginjal',
                        'rows' => [
                            ['Ureum', $record->ureum, '< 50.0', 'mg/dL'],
                            ['Kreatinin', $record->kreatinin, '0.6 - 1.1', 'mg/dL'],
                            ['Asam Urat', $record->asam_urat, '2.4 - 6.1', 'mg/dL'],
                            ['HBeAg', $record->hbeag, 'Non Reaktif', ''],
                        ],
                    ],
                    [
                        'title' => 'Fungsi Hati',
                        'rows' => [
                            ['SGOT', $record->sgot, '< 35.00', 'µ/L'],
                            ['SGPT', $record->sgpt, '< 45.00', 'µ/L'],
                            ['Alkali Fosfatase', $record->alkali_fosfatase, '30 - 120', 'µ/L'],
                            ['Kolinesterase', $record->kolinesterase, '4620 - 11250', 'µ/L'],
                            ['Bilirubin Total', $record->bilirubin_total, '0.00 - 1.00', 'mg/dL'],
                            ['Bilirubin Direk', $record->bilirubin_direk, '0.00 - 0.30', 'mg/dL'],
                            ['Bilirubin InDirek', $record->bilirubin_indirek, '0.00 - 0.70', ''],
                        ],
                    ],
                    [
                        'title' => 'Profil Lemak',
                        'rows' => [
                            ['Kolesterol Total', $record->kolesterol_total, '50 - 200', 'mg/dL'],
                            ['HDL', $record->hdl, '37 - 92', 'mg/dL'],
                            ['LDL', $record->ldl, '97 - 202', 'mg/dL'],
                            ['Trigliserida', $record->trigliserida, '< 150.0', 'mg/dL'],
                            ['HbA1c', $record->hba1c, '4.8 - 5.9', '%'],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'SEROLOGI',
                'rows' => [
                    ['TPHA', $record->tpha, 'Non Reaktif', ''],
                    ['VDRL', $record->vdrl, 'Non Reaktif', ''],
                ],
            ],
            [
                'title' => 'IMUNOLOGI',
                'rows' => [
                    ['HBsAg', $record->hbsag, 'Non Reaktif', ''],
                    ['Anti HCV', $record->anti_hcv, 'Non Reaktif', ''],
                    ['Anti HBS', $record->anti_hbs, 'Non Reaktif', ''],
                ],
            ],
            [
                'title' => 'SKRINING NARKOBA',
                'rows' => [
                    ['Amphetamine', $record->narkoba_amphetamine, 'Negatif', ''],
                    ['THC (Canabis)', $record->narkoba_thc, 'Negatif', ''],
                    ['Morphine', $record->narkoba_morphine, 'Negatif', ''],
                    ['Benzodiazepine', $record->narkoba_benzodiazepine, 'Negatif', ''],
                    ['Methamphetamine', $record->narkoba_methamphetamine, 'Negatif', ''],
                    ['Cocaine', $record->narkoba_cocaine, 'Negatif', ''],
                    ['Alkohol Urin', $record->alkohol_urin, 'Negatif', ''],
                ],
            ],
        ];

<<<<<<< HEAD
        $renderRow = function ($label, $value, $rujukan, $satuan) {
            $keterangan = str_contains((string) $value, '*') ? 'Perhatikan' : '';
            $isAbnormal = false;
            
            // Check if value is numeric and has reference range
            if (is_numeric($value) && $rujukan && !in_array($rujukan, ['Negatif', 'Normal', 'Non Reaktif', 'Kuning', 'Jernih', ''])) {
                $isAbnormal = isValueAbnormal($value, $rujukan);
            }
            
            return [$label, $value, $rujukan, $satuan, $keterangan, $isAbnormal];
=======
        $parseNumericTokens = function (?string $text): array {
            if ($text === null) {
                return [];
            }

            preg_match_all('/-?\d+(?:[.,]\d+)?/', str_replace(',', '.', $text), $matches);
            return array_map('floatval', $matches[0] ?? []);
        };

        $isAbnormal = function ($value, $rujukan) use ($parseNumericTokens): bool {
            if ($value === null || $value === '' || $rujukan === null || trim((string) $rujukan) === '') {
                return false;
            }

            $valueRaw = trim((string) $value);
            $rujukanRaw = trim((string) $rujukan);
            $valueNormalized = mb_strtolower(str_replace(['.', ','], ['', '.'], $valueRaw));
            $rujukanNormalized = mb_strtolower(str_replace(['.', ','], ['', '.'], $rujukanRaw));

            if (preg_match('/^(-?\d+(?:\.\d+)?)\s*-\s*(-?\d+(?:\.\d+)?)$/', $rujukanNormalized, $rangeMatch)) {
                $valueNumbers = $parseNumericTokens($valueRaw);
                if ($valueNumbers === []) {
                    return false;
                }

                $valueMin = min($valueNumbers);
                $valueMax = max($valueNumbers);
                $min = (float) $rangeMatch[1];
                $max = (float) $rangeMatch[2];

                return $valueMin < $min || $valueMax > $max;
            }

            if (preg_match('/^(<=|<|>=|>)\s*(-?\d+(?:\.\d+)?)$/', $rujukanNormalized, $operatorMatch)) {
                $valueNumbers = $parseNumericTokens($valueRaw);
                if ($valueNumbers === []) {
                    return false;
                }

                $valueToCompare = in_array($operatorMatch[1], ['<', '<='], true) ? max($valueNumbers) : min($valueNumbers);
                $limit = (float) $operatorMatch[2];

                return match ($operatorMatch[1]) {
                    '<' => !($valueToCompare < $limit),
                    '<=' => !($valueToCompare <= $limit),
                    '>' => !($valueToCompare > $limit),
                    '>=' => !($valueToCompare >= $limit),
                };
            }

            if (in_array($rujukanNormalized, ['negatif', 'non reaktif', 'normal', 'jernih', 'kuning'], true)) {
                return !str_contains($valueNormalized, $rujukanNormalized);
            }

            return false;
        };

        $renderRow = function ($label, $value, $rujukan, $satuan) use ($isAbnormal) {
            $abnormal = $isAbnormal($value, $rujukan);
            $displayValue = (string) ($value ?? '');

            if ($abnormal && !str_ends_with(trim($displayValue), '*')) {
                $displayValue = trim($displayValue) . '*';
            }

            $keterangan = $abnormal ? 'Abnormal' : '';

            return [$label, $displayValue, $rujukan, $satuan, $keterangan, $abnormal];
>>>>>>> cff07beb9c79936835762b93a3d8cb07736b72ec
        };
        
        function isValueAbnormal($value, $rujukan) {
            // Handle range formats like "13.5 - 17.5", "< 50.0", "> 100", "0 - 20"
            if (strpos($rujukan, '-') !== false) {
                // Range format: "min - max"
                $parts = explode('-', $rujukan);
                if (count($parts) == 2) {
                    $min = trim($parts[0]);
                    $max = trim($parts[1]);
                    
                    // Handle special cases like "< 5" or "> 100"
                    if (strpos($min, '<') !== false) {
                        $maxVal = trim(str_replace('<', '', $min));
                        return $value >= $maxVal;
                    }
                    if (strpos($max, '>') !== false) {
                        $minVal = trim(str_replace('>', '', $max));
                        return $value <= $minVal;
                    }
                    
                    // Normal range
                    return $value < $min || $value > $max;
                }
            } elseif (strpos($rujukan, '<') !== false) {
                // Upper limit: "< 50.0"
                $maxVal = trim(str_replace('<', '', $rujukan));
                return $value >= $maxVal;
            } elseif (strpos($rujukan, '>') !== false) {
                // Lower limit: "> 100"
                $minVal = trim(str_replace('>', '', $rujukan));
                return $value <= $minVal;
            }
            
            return false;
        }
    @endphp

    <div class="container">
        <table class="info-table header-with-border">
            <tr>
                <td>No. RM</td>
                <td>:</td>
                <td><span class="bold">{{ $record->no_rm }}</span></td>
                <td>No. Lab</td>
                <td>:</td>
                <td><span class="bold">{{ $record->no_lab }}</span></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><span class="bold">{{ $record->participant?->name }}</span></td>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $record->participant?->gender }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->translatedFormat('j F Y') }}</td>
                <td>Instansi</td>
                <td>:</td>
                <td>{{ $record->instansi }}</td>
            </tr>
            <tr>
                <td>Usia</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }}</td>
            </tr>
        </table>

        <table class="main-table mt-10">
            <thead>
                <tr>
                    <th class="pemeriksaan-col">PEMERIKSAAN</th>
                    <th class="hasil-col">HASIL</th>
                    <th class="rujukan-col">NILAI RUJUKAN</th>
                    <th class="satuan-col">SATUAN</th>
                    <th class="keterangan-col">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pageOneSections as $section)
                    <tr class="section-header">
                        <td colspan="5">{{ $section['title'] }}</td>
                    </tr>

                    @foreach ($section['rows'] ?? [] as $row)
<<<<<<< HEAD
                        @php [$label, $value, $rujukan, $satuan, $keterangan, $isAbnormal] = $renderRow(...$row); @endphp
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="center{{ $isAbnormal ? ' abnormal-value' : '' }}">
                                {{ $isAbnormal ? $value . '*' : $value }}
                            </td>
=======
                        @php [$label, $value, $rujukan, $satuan, $keterangan, $abnormal] = $renderRow(...$row); @endphp
                        <tr>
                            <td>{{ $label }}</td>
                            <td class="center {{ $abnormal ? 'abnormal-value' : '' }}">{{ $value }}</td>
>>>>>>> cff07beb9c79936835762b93a3d8cb07736b72ec
                            <td class="center">{{ $rujukan }}</td>
                            <td class="center">{{ $satuan }}</td>
                            <td class="{{ $abnormal ? 'abnormal-value' : '' }}">{{ $keterangan }}</td>
                        </tr>
                    @endforeach

                    @foreach ($section['subsections'] ?? [] as $subsection)
                        <tr class="sub-section-header">
                            <td colspan="5">{{ $subsection['title'] }}</td>
                        </tr>
                        @foreach ($subsection['rows'] as $row)
<<<<<<< HEAD
                            @php [$label, $value, $rujukan, $satuan, $keterangan, $isAbnormal] = $renderRow(...$row); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="center{{ $isAbnormal ? ' abnormal-value' : '' }}">
                                    {{ $isAbnormal ? $value . '*' : $value }}
                                </td>
=======
                            @php [$label, $value, $rujukan, $satuan, $keterangan, $abnormal] = $renderRow(...$row); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="center {{ $abnormal ? 'abnormal-value' : '' }}">{{ $value }}</td>
>>>>>>> cff07beb9c79936835762b93a3d8cb07736b72ec
                                <td class="center">{{ $rujukan }}</td>
                                <td class="center">{{ $satuan }}</td>
                                <td class="{{ $abnormal ? 'abnormal-value' : '' }}">{{ $keterangan }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <div class="note mt-10">
            Hasil berupa angka menggunakan sistem desimal dengan separator titik.<br>
            Tanda * menunjukkan nilai di atas atau di bawah nilai rujukan.
        </div>
    </div>

    <div class="print-page">
        <div class="container">
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="pemeriksaan-col">PEMERIKSAAN</th>
                        <th class="hasil-col">HASIL</th>
                        <th class="rujukan-col">NILAI RUJUKAN</th>
                        <th class="satuan-col">SATUAN</th>
                        <th class="keterangan-col">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pageTwoSections as $section)
                        <tr class="section-header">
                            <td colspan="5">{{ $section['title'] }}</td>
                        </tr>

                        @foreach ($section['rows'] ?? [] as $row)
<<<<<<< HEAD
                            @php [$label, $value, $rujukan, $satuan, $keterangan, $isAbnormal] = $renderRow(...$row); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="center{{ $isAbnormal ? ' abnormal-value' : '' }}">
                                    {{ $isAbnormal ? $value . '*' : $value }}
                                </td>
=======
                            @php [$label, $value, $rujukan, $satuan, $keterangan, $abnormal] = $renderRow(...$row); @endphp
                            <tr>
                                <td>{{ $label }}</td>
                                <td class="center {{ $abnormal ? 'abnormal-value' : '' }}">{{ $value }}</td>
>>>>>>> cff07beb9c79936835762b93a3d8cb07736b72ec
                                <td class="center">{{ $rujukan }}</td>
                                <td class="center">{{ $satuan }}</td>
                                <td class="{{ $abnormal ? 'abnormal-value' : '' }}">{{ $keterangan }}</td>
                            </tr>
                        @endforeach

                        @foreach ($section['subsections'] ?? [] as $subsection)
                            <tr class="sub-section-header">
                                <td colspan="5">{{ $subsection['title'] }}</td>
                            </tr>
                            @foreach ($subsection['rows'] as $row)
<<<<<<< HEAD
                                @php [$label, $value, $rujukan, $satuan, $keterangan, $isAbnormal] = $renderRow(...$row); @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="center{{ $isAbnormal ? ' abnormal-value' : '' }}">
                                        {{ $isAbnormal ? $value . '*' : $value }}
                                    </td>
=======
                                @php [$label, $value, $rujukan, $satuan, $keterangan, $abnormal] = $renderRow(...$row); @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="center {{ $abnormal ? 'abnormal-value' : '' }}">{{ $value }}</td>
>>>>>>> cff07beb9c79936835762b93a3d8cb07736b72ec
                                    <td class="center">{{ $rujukan }}</td>
                                    <td class="center">{{ $satuan }}</td>
                                    <td class="{{ $abnormal ? 'abnormal-value' : '' }}">{{ $keterangan }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="note mt-10">
                Hasil berupa angka menggunakan sistem desimal dengan separator titik.<br>
                Tanda * menunjukkan nilai di atas atau di bawah nilai rujukan.
            </div>

            <div class="footer">
                Penanggung Jawab Laboratorium
                <div class="signature-area">
                    @if ($record->tanda_tangan)
                        <img src="{{ \App\Support\StoragePublicUrl::fromPath($record->tanda_tangan) }}" class="ttd-image"
                            alt="TTD">
                    @endif
                </div>
                <span class="bold underline">{{ $record->penanggung_jawab }}</span>
            </div>
        </div>
    </div>
</body>

</html>
