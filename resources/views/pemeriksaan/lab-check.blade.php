<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Lab - {{ $record->participant?->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .container {
            width: 95%;
            margin: auto;
        }

        .header-table,
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 4px 6px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #555;
            padding: 4px 6px;
        }

        .main-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .section-header td {
            background-color: #f0f0f0;
            font-weight: bold;
            text-decoration: underline;
        }

        .sub-section-header td {
            padding-left: 15px !important;
            font-style: italic;
        }

        .col-pemeriksaan {
            width: 25%;
        }

        .col-hasil {
            width: 15%;
            text-align: center;
        }

        .col-rujukan {
            width: 20%;
            text-align: center;
        }

        .col-satuan {
            width: 15%;
            text-align: center;
        }

        .col-keterangan {
            width: 25%;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
        }

        .signature-area {
            height: 80px;
        }

        .page-break {
            page-break-before: always;
        }

        .note {
            font-size: 9px;
        }
    </style>
</head>

<body>
    <!-- HALAMAN PERTAMA -->
    <div class="container">
        <table class="header-table">
            <tr>
                <td>No. RM</td>
                <td>: <b>{{ $record->no_rm }}</b></td>
                <td>No. Lab</td>
                <td>: <b>{{ $record->no_lab }}</b></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <b>{{ $record->participant?->name }}</b></td>
                <td>Jenis Kelamin</td>
                <td>: {{ $record->participant?->gender }}</td>
            </tr>
            <tr>
                <td>Tanggal Lahir</td>
                <td>: {{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d-m-Y') }}</td>
                <td>Instansi</td>
                <td>: {{ $record->instansi }}</td>
            </tr>
            <tr>
                <td>Usia</td>
                <td>: {{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <table class="main-table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th class="col-pemeriksaan">PEMERIKSAAN</th>
                    <th class="col-hasil">HASIL</th>
                    <th class="col-rujukan">NILAI RUJUKAN</th>
                    <th class="col-satuan">SATUAN</th>
                    <th class="col-keterangan">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td colspan="5">HEMATOLOGI LENGKAP</td>
                </tr>
                <tr>
                    <td>Hemoglobin</td>
                    <td>{{ $record->hemoglobin }}*</td>
                    <td>13.5 - 17.5</td>
                    <td>g/dL</td>
                    <td>Low</td>
                </tr>
                <tr>
                    <td>Leukosit</td>
                    <td>{{ $record->leukosit }}</td>
                    <td>4.5 - 11.0</td>
                    <td>10³/uL</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Trombosit</td>
                    <td>{{ $record->trombosit }}*</td>
                    <td>150 - 450</td>
                    <td>10³/uL</td>
                    <td>Low</td>
                </tr>
                <tr>
                    <td>Hematokrit</td>
                    <td>{{ $record->hematokrit }}</td>
                    <td>35.0 - 45.0</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Eritrosit</td>
                    <td>{{ $record->eritrosit }}*</td>
                    <td>4.50 - 5.90</td>
                    <td>10⁶/uL</td>
                    <td>Low</td>
                </tr>
                <tr>
                    <td>MCV</td>
                    <td>{{ $record->mcv }}</td>
                    <td>80.0 - 36.0</td>
                    <td>fl</td>
                    <td></td>
                </tr>
                <tr>
                    <td>MCH</td>
                    <td>{{ $record->mch }}</td>
                    <td>28.0 - 33.0</td>
                    <td>pg</td>
                    <td></td>
                </tr>
                <tr>
                    <td>MCHC</td>
                    <td>{{ $record->mchc }}</td>
                    <td>33.0 - 36.0</td>
                    <td>g/dL</td>
                    <td></td>
                </tr>
                <tr>
                    <td>RDW</td>
                    <td>{{ $record->rdw }}</td>
                    <td>11.6 - 14.6</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr class="sub-section-header">
                    <td colspan="5">Hitung Jenis Leukosit</td>
                </tr>
                <tr>
                    <td>Eosinofil</td>
                    <td>{{ $record->eosinofil }}</td>
                    <td>0 - 4</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Basofil</td>
                    <td>{{ $record->basofil }}</td>
                    <td>0 - 2</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Netrofil Batang</td>
                    <td>{{ $record->netrofil_batang }}</td>
                    <td>2 - 6</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Netrofil Segmen</td>
                    <td>{{ $record->netrofil_segmen }}</td>
                    <td>55 - 80</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Limfosit</td>
                    <td>{{ $record->limfosit }}</td>
                    <td>22 - 44</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Monosit</td>
                    <td>{{ $record->monosit }}</td>
                    <td>0 - 7</td>
                    <td>%</td>
                    <td></td>
                </tr>
                <tr>
                    <td>LED</td>
                    <td>{{ $record->led }}</td>
                    <td>0 - 20</td>
                    <td>mm/jam</td>
                    <td></td>
                </tr>

                <tr class="section-header">
                    <td colspan="5">URINALISA</td>
                </tr>
                <tr class="sub-section-header">
                    <td colspan="5">Urine Lengkap</td>
                </tr>
                <tr>
                    <td>Warna</td>
                    <td>{{ $record->urine_warna }}</td>
                    <td>Kuning</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Kejernihan</td>
                    <td>{{ $record->urine_kejernihan }}</td>
                    <td>Jernih</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Berat Jenis</td>
                    <td>{{ $record->urine_berat_jenis }}</td>
                    <td>1.015 - 1.025</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>pH</td>
                    <td>{{ $record->urine_ph }}</td>
                    <td>4.5 - 8.0</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Protein (Albumin)</td>
                    <td>{{ $record->urine_protein }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Glukosa</td>
                    <td>{{ $record->urine_glukosa }}</td>
                    <td>Normal</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Keton</td>
                    <td>{{ $record->urine_keton }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Darah / Hb</td>
                    <td>{{ $record->urine_darah }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Bilirubin</td>
                    <td>{{ $record->urine_bilirubin }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Urobilinogen</td>
                    <td>{{ $record->urine_urobilinogen }}</td>
                    <td>0.2 - 1.0</td>
                    <td>mg/dL</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Nitrit</td>
                    <td>{{ $record->urine_nitrit }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Leukosit Esterase</td>
                    <td>{{ $record->urine_leukosit_esterase }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="sub-section-header">
                    <td colspan="5">Sedimen</td>
                </tr>
                <tr>
                    <td>Leukosit</td>
                    <td>{{ $record->sedimen_leukosit }}</td>
                    <td>
                        <5< /td>
                    <td>/LPB</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Eritrosit</td>
                    <td>{{ $record->sedimen_eritrosit }}</td>
                    <td>
                        <3< /td>
                    <td>/LPB</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Silinder</td>
                    <td>{{ $record->sedimen_silinder }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Sel Epitel</td>
                    <td>{{ $record->sedimen_sel_epitel }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Kristal</td>
                    <td>{{ $record->sedimen_kristal }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Bakteria</td>
                    <td>{{ $record->sedimen_bakteria }}</td>
                    <td>Negatif</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Lain - lain</td>
                    <td>{{ $record->sedimen_lain_lain }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <div class="note">
            Hasil berupa angka menggunakan sistem desimal dengan separator titik.<br>
            Tanda * menunjukkan nilai di atas atau di bawah nilai rujukan.
        </div>
    </div>

    <!-- HALAMAN KEDUA -->
    <div class="page-break">
        <div class="container">
            <table class="main-table" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>PEMERIKSAAN</th>
                        <th>HASIL</th>
                        <th>NILAI RUJUKAN</th>
                        <th>SATUAN</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="section-header">
                        <td colspan="5">KIMIA KLINIK</td>
                    </tr>
                    <tr>
                        <td>Glukosa Puasa</td>
                        <td>{{ $record->glukosa_puasa }}</td>
                        <td>70 - 110</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Glukosa 2 Jam Pp</td>
                        <td>{{ $record->glukosa_2_jam_pp }}</td>
                        <td>80 - 140</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr class="sub-section-header">
                        <td colspan="5">Fungsi Ginjal</td>
                    </tr>
                    <tr>
                        <td>Ureum</td>
                        <td>{{ $record->ureum }}</td>
                        <td>
                            < 50.0</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Kreatinin</td>
                        <td>{{ $record->kreatinin }}</td>
                        <td>0.6 - 1.1</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Asam Urat</td>
                        <td>{{ $record->asam_urat }}</td>
                        <td>2.4 - 6.1</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>HBeAg</td>
                        <td>{{ $record->hbeag }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="sub-section-header">
                        <td colspan="5">Fungsi Hati</td>
                    </tr>
                    <tr>
                        <td>SGOT</td>
                        <td>{{ $record->sgot }}</td>
                        <td>
                            < 35.00</td>
                        <td>µ/L</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>SGPT</td>
                        <td>{{ $record->sgpt }}</td>
                        <td>
                            < 45.00</td>
                        <td>µ/L</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Alkali Fosfatase</td>
                        <td>{{ $record->alkali_fosfatase }}</td>
                        <td>30 - 120</td>
                        <td>µ/L</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Kolinesterase</td>
                        <td>{{ $record->kolinesterase }}</td>
                        <td>4620 - 11250</td>
                        <td>µ/L</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Bilirubin Total</td>
                        <td>{{ $record->bilirubin_total }}</td>
                        <td>0.00 - 1.00</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Bilirubin Direk</td>
                        <td>{{ $record->bilirubin_direk }}</td>
                        <td>0.00 - 0.30</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Bilirubin InDirek</td>
                        <td>{{ $record->bilirubin_indirek }}</td>
                        <td>0.00 - 0.70</td>
                        <td>-</td>
                        <td></td>
                    </tr>
                    <tr class="sub-section-header">
                        <td colspan="5">Profil Lemak</td>
                    </tr>
                    <tr>
                        <td>Kolesterol Total</td>
                        <td>{{ $record->kolesterol_total }}</td>
                        <td>50 - 200</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>HDL</td>
                        <td>{{ $record->hdl }}</td>
                        <td>37 - 92</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>LDL</td>
                        <td>{{ $record->ldl }}</td>
                        <td>97 - 202</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Trigliserida</td>
                        <td>{{ $record->trigliserida }}</td>
                        <td>
                            < 150.0</td>
                        <td>mg/dL</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>HbA1c</td>
                        <td>{{ $record->hba1c }}</td>
                        <td>4.8 - 5.9</td>
                        <td>%</td>
                        <td></td>
                    </tr>

                    <tr class="section-header">
                        <td colspan="5">SEROLOGI</td>
                    </tr>
                    <tr>
                        <td>TPHA</td>
                        <td>{{ $record->tpha }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>VDRL</td>
                        <td>{{ $record->vdrl }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header">
                        <td colspan="5">IMUNOLOGI</td>
                    </tr>
                    <tr>
                        <td>HBsAg</td>
                        <td>{{ $record->hbsag }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Anti HCV</td>
                        <td>{{ $record->anti_hcv }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Anti HBS</td>
                        <td>{{ $record->anti_hbs }}</td>
                        <td>Non Reaktif</td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr class="section-header">
                        <td colspan="5">SKRINING NARKOBA</td>
                    </tr>
                    <tr>
                        <td>Amphetamine</td>
                        <td>{{ $record->narkoba_amphetamine }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>THC (Canabis)</td>
                        <td>{{ $record->narkoba_thc }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Morphine</td>
                        <td>{{ $record->narkoba_morphine }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Benzodiazepine</td>
                        <td>{{ $record->narkoba_benzodiazepine }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Methamphetamine</td>
                        <td>{{ $record->narkoba_methamphetamine }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cocaine</td>
                        <td>{{ $record->narkoba_cocaine }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Alkohol Urin</td>
                        <td>{{ $record->alkohol_urin }}</td>
                        <td>Negatif</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="note" style="margin-top: 20px;">
                Hasil berupa angka menggunakan sistem desimal dengan separator titik.<br>
                Tanda * menunjukkan nilai di atas atau di bawah nilai rujukan.
            </div>
            <div class="footer">
                Penanggung Jawab Laboratorium
                <div class="signature-area">
                    @if ($record->tanda_tangan)
                        <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}"
                            style="max-height: 80px;" alt="TTD">
                    @endif
                </div>
                <b><u>{{ $record->penanggung_jawab }}</u></b>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            window.print();
        }, 500);
    </script>
</body>

</html>
