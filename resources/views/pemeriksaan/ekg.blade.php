<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan EKG - {{ $record->participant?->name }}</title>
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

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table td {
            padding: 6px;
            vertical-align: top;
        }

        .label {
            width: 15%;
        }

        .separator {
            width: 2%;
        }

        .value {
            width: 33%;
        }

        .signature-area {
            height: 120px;
            text-align: center;
        }

        .signature-area img {
            max-height: 80px;
        }

        .doctor-name {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }

        /* ======================================================= */
        /*              PERUBAHAN CSS UNTUK HALAMAN 2              */
        /* ======================================================= */
        .page-break {
            page-break-before: always;
        }

        .page-2-layout {
            display: flex;
            flex-direction: column;
            height: 95vh;
        }

        /* Membuat layout flex vertikal */
        .ekg-image-container {
            flex-grow: 1;
            /* Membuat container gambar mengisi sisa ruang */
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .ekg-image-container img {
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
        @include('pemeriksaan.partials.ekg-patient-header')

        <table class="content-table" style="border: 2px solid black;">
            <!-- Garis Pemisah -->
            <tr>
                <td colspan="6" style="border-bottom: 2px solid black; padding: 5px;"></td>
            </tr>
            <!-- Baris Hasil Interpretasi -->
            <tr>
                <td class="label">Irama</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->irama }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Heart Rate</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->heart_rate }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Axis</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->axis }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">PR Interval</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->pr_interval }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">QRS Duration</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->qrs_duration }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Gel T</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->gel_t }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">ST-T Changes</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->st_t_changes }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Kelainan</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->kelainan }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <!-- Garis Pemisah -->
            <tr>
                <td colspan="6" style="border-bottom: 2px solid black; padding: 5px;"></td>
            </tr>
            <!-- Baris Kesimpulan & TTD -->
            <tr>
                <td class="label"><b>Kesimpulan</b></td>
                <td class="separator">:</td>
                <td><b>{{ $record->kesimpulan }}</b></td>
                <td colspan="3">
                    <div style="text-align: center;">Cardiologist</div>
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}"
                                alt="Tanda Tangan">
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
            @include('pemeriksaan.partials.ekg-patient-header')
        </div>

        <div class="ekg-image-container">
            @if ($record->gambar_hasil_ekg)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_ekg) }}" alt="Hasil EKG">
            @else
                <p>Gambar hasil EKG tidak tersedia.</p>
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
