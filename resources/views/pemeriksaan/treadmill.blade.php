<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Treadmill - {{ $record->participant?->name }}</title>
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
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .header h2,
        .header h3 {
            margin: 0;
            padding: 0;
            text-decoration: underline;
            font-weight: bold;
        }

        .info-table,
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td,
        .content-table td {
            padding: 4px;
            vertical-align: top;
        }

        .info-table .label {
            width: 15%;
        }

        .info-table .separator {
            width: 2%;
        }

        .info-table .value {
            width: 33%;
        }

        .content-table .label {
            width: 20%;
        }

        .content-table .separator {
            width: 2%;
        }

        .sub-label {
            padding-left: 20px !important;
        }

        .signature-area {
            height: 100px;
        }

        .doctor-name {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }

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
    <!-- HALAMAN PERTAMA -->
    <div class="container">
        <div class="header">
            <h2>HASIL PEMERIKSAAN TREADMILL</h2>
            <h3>( TREADMILL TEST )</h3>
        </div>

        @include('pemeriksaan.partials.treadmill-patient-header')

        <table class="content-table" style="border: 2px solid black; padding: 5px;">
            <tr>
                <td class="label">Metode</td>
                <td class="separator">:</td>
                <td>{{ $record->metode }}</td>
            </tr>
            <tr>
                <td class="label">EKG Resting</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_resting }}</td>
            </tr>
            <tr>
                <td class="label">EKG Exercise</td>
                <td class="separator">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label sub-label">ST-T Change Segmen</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_exercise_st_change }}</td>
            </tr>
            <tr>
                <td class="label sub-label">Aritmia</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_exercise_aritmia }}</td>
            </tr>
            <tr>
                <td class="label">Tekanan Darah</td>
                <td class="separator">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label sub-label">Awal</td>
                <td class="separator">:</td>
                <td>{{ $record->td_awal }} mm/Hg</td>
            </tr>
            <tr>
                <td class="label sub-label">Tertinggi</td>
                <td class="separator">:</td>
                <td>{{ $record->td_tertinggi }} mm/Hg</td>
            </tr>
            <tr>
                <td class="label">Indikasi Berhenti</td>
                <td class="separator">:</td>
                <td>{{ $record->indikasi_berhenti }}</td>
            </tr>
            <tr>
                <td class="label">Target HR</td>
                <td class="separator">:</td>
                <td>{{ $record->target_hr }}</td>
            </tr>
            <tr>
                <td class="label">Tercapai</td>
                <td class="separator">:</td>
                <td>{{ $record->tercapai_hr }}</td>
            </tr>
            <tr>
                <td class="label">Lama Test</td>
                <td class="separator">:</td>
                <td>{{ $record->lama_tes_menit }} Menit {{ $record->lama_tes_detik }} Detik</td>
            </tr>
            <tr>
                <td colspan="3" style="border-bottom: 2px solid black; padding: 2px;"></td>
            </tr>
            <tr>
                <td class="label">Kapasitas Aerobik</td>
                <td class="separator">:</td>
                <td>{{ $record->kapasitas_aerobik }} Mets</td>
            </tr>
            <tr>
                <td class="label">Kelas Fungsional</td>
                <td class="separator">:</td>
                <td>{{ $record->kelas_fungsional }}</td>
            </tr>
            <tr>
                <td class="label">Tingkat Kebugaran</td>
                <td class="separator">:</td>
                <td>{{ $record->tingkat_kebugaran }}</td>
            </tr>
            <tr>
                <td colspan="3" style="border-bottom: 2px solid black; padding: 2px;"></td>
            </tr>
            <tr>
                <td colspan="3">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 60%; vertical-align: top;">
                                <b><u>Kesimpulan</u></b> : {{ $record->kesimpulan }}
                                <br><br>
                                <b><u>Saran</u></b> : <div style="white-space: pre-wrap;">{{ $record->saran }}</div>
                            </td>
                            <td style="width: 40%; text-align: center; vertical-align: top;">
                                Cardiologist
                                <div class="signature-area">
                                    @if ($record->tanda_tangan)
                                        <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}"
                                            style="max-height: 80px;" alt="TTD">
                                    @endif
                                </div>
                                <div class="doctor-name">{{ $record->cardiologist }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN KEDUA -->
    <div class="page-break page-2-layout">
        <div class="container">
            @include('pemeriksaan.partials.treadmill-patient-header')
        </div>
        <div class="image-container">
            @if ($record->gambar_hasil_treadmill)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_treadmill) }}"
                    alt="Hasil Treadmill">
            @else
                <p>Gambar hasil treadmill tidak tersedia.</p>
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
