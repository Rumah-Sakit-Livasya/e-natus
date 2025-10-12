<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Rontgen - {{ $record->participant?->name }}</title>
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

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
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

        .main-content {
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }

        .findings-text {
            white-space: pre-wrap;
            margin-left: 20px;
        }

        .conclusion-table td {
            padding: 6px;
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
            <h2>HASIL PEMERIKSAAN RONTGEN</h2>
            <h3>( RADIOLOGI )</h3>
        </div>

        @include('pemeriksaan.partials.rontgen-patient-header')

        <div class="main-content">
            <b><u>Yth, TS.</u></b>
            <div class="findings-text">{!! nl2br(e($record->temuan)) !!}</div>
        </div>

        <table class="info-table conclusion-table" style="border: 2px solid black;">
            <tr>
                <td style="width: 17%;"><b><u>Kesan</u></b></td>
                <td style="width: 2%;">:</td>
                <td><b>{{ $record->kesan }}</b></td>
                <td style="width: 40%; text-align: center;">
                    Radiologist
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}"
                                style="max-height: 80px;" alt="TTD">
                        @endif
                    </div>
                    <div class="doctor-name">{{ $record->radiologist }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN KEDUA -->
    <div class="page-break page-2-layout">
        <div class="container">
            @include('pemeriksaan.partials.rontgen-patient-header')
        </div>
        <div class="image-container">
            @if ($record->gambar_hasil_rontgen)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_rontgen) }}"
                    alt="Hasil Rontgen">
            @else
                <p>Gambar hasil rontgen tidak tersedia.</p>
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
