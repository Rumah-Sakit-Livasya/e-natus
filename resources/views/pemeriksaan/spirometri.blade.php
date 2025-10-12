<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Spirometri - {{ $record->participant?->name }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .info-table,
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 2px solid #333;
        }


        .info-table td {
            padding: 4px;
        }

        .info-table .label {
            width: 15%;
        }

        .info-table .separator {
            width: 2%;
        }

        .result-table th,
        .result-table td {
            border: 2px solid #333;
            padding: 8px;
            text-align: center;
        }

        .result-table th {
            background-color: #e0e0e0;
        }

        .result-table .pemeriksaan {
            text-align: left;
        }

        .conclusion-table td {
            padding: 6px;
            vertical-align: top;
        }

        .signature-area {
            height: 100px;
        }

        .doctor-name {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }

        /* ======================================================= */
        /*              CSS UNTUK HALAMAN 2                        */
        /* ======================================================= */
        .page-break {
            page-break-before: always;
        }

        .page-2-layout {
            display: flex;
            flex-direction: column;
            height: 95vh;
        }

        .image-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        @media print {
            body {
                margin: 0;
                padding: 1cm;
            }

            .container {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- ======================================================= -->
    <!--                       HALAMAN PERTAMA                     -->
    <!-- ======================================================= -->
    <div class="container">

        {{-- Menggunakan partial header pasien --}}
        @include('pemeriksaan.partials.spirometri-patient-header')

        <table class="result-table">
            <thead>
                <tr>
                    <th>Pemeriksaan</th>
                    <th>Nilai</th>
                    <th>Prediksi</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="pemeriksaan">Best VC (ml)</td>
                    <td>{{ $record->vc_nilai }}</td>
                    <td>{{ $record->vc_prediksi }}</td>
                    <td>
                        @if ($record->vc_prediksi > 0)
                            {{ number_format(($record->vc_nilai / $record->vc_prediksi) * 100, 2) }} %
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pemeriksaan">Best FVC (ml)</td>
                    <td>{{ $record->fvc_nilai }}</td>
                    <td>{{ $record->fvc_prediksi }}</td>
                    <td>
                        @if ($record->fvc_prediksi > 0)
                            {{ number_format(($record->fvc_nilai / $record->fvc_prediksi) * 100, 2) }} %
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pemeriksaan">Best FEV1 (ml)</td>
                    <td>{{ $record->fev1_nilai }}</td>
                    <td>{{ $record->fev1_prediksi }}</td>
                    <td>
                        @if ($record->fev1_prediksi > 0)
                            {{ number_format(($record->fev1_nilai / $record->fev1_prediksi) * 100, 2) }} %
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="pemeriksaan">FEV1 / FVC (%)</td>
                    <td>
                        @if ($record->fvc_nilai > 0)
                            {{ number_format($record->fev1_nilai / $record->fvc_nilai, 4) }}
                        @endif
                    </td>
                    <td>
                        @if ($record->fvc_prediksi > 0)
                            {{ number_format($record->fev1_prediksi / $record->fvc_prediksi, 4) }}
                        @endif
                    </td>
                    <td>
                        @php
                            $nilai_ratio = $record->fvc_nilai > 0 ? $record->fev1_nilai / $record->fvc_nilai : 0;
                            $pred_ratio =
                                $record->fvc_prediksi > 0 ? $record->fev1_prediksi / $record->fvc_prediksi : 0;
                        @endphp
                        @if ($pred_ratio > 0)
                            {{ number_format(($nilai_ratio / $pred_ratio) * 100, 2) }} %
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="info-table conclusion-table">
            <tr>
                <td class="label"><b>Kesan</b></td>
                <td class="separator">:</td>
                <td>{{ $record->kesan }}</td>
            </tr>
            <tr>
                <td class="label"><b>Saran</b></td>
                <td class="separator">:</td>
                <td>{{ $record->saran }}</td>
            </tr>
        </table>

        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="text-align: center;">
                    Dokter Pemeriksa,
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}"
                                style="max-height: 80px;" alt="TTD">
                        @endif
                    </div>
                    <div class="doctor-name">{{ $record->dokter_pemeriksa }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ======================================================= -->
    <!--                        HALAMAN KEDUA                      -->
    <!-- ======================================================= -->
    <div class="page-break page-2-layout">
        <div class="container">
            {{-- Menggunakan partial header pasien LAGI di sini --}}
            @include('pemeriksaan.partials.spirometri-patient-header')
        </div>

        <div class="image-container">
            @if ($record->gambar_hasil_spirometri)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_spirometri) }}"
                    alt="Hasil Spirometri">
            @else
                <p>Gambar hasil spirometri tidak tersedia.</p>
            @endif
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.print();
        }, 500);
    </script>
</body>

</html>
