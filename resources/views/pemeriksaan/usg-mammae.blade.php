<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil USG Mammae - {{ $record->participant?->name }}</title>
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

        .content-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #555;
        }

        .content-table td {
            padding: 8px 6px;
            vertical-align: top;
            border: 1px solid #555;
        }

        .findings-header {
            font-weight: bold;
            text-decoration: underline;
        }

        .findings-text {
            white-space: pre-wrap;
            margin-left: 20px;
        }

        .conclusion-table td {
            padding: 6px;
            border: 1px solid #555;
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
        @include('pemeriksaan.partials.usg-mammae-patient-header')

        <table class="content-table">
            <tr>
                <td colspan="1" style="border-left: 0; border-right:0; border-top:0;"><b><u>Telah dilakukan pemeriksaan
                            USG Mammae bilateral, dengan hasil:</u></b></td>
            </tr>
            <tr>
                <td>
                    <div class="findings-header">Mammae Kanan:</div>
                    <div class="findings-text">{!! nl2br(e($record->mammae_kanan)) !!}</div>
                    <br>
                    <div class="findings-header">Mammae Kiri:</div>
                    <div class="findings-text">{!! nl2br(e($record->mammae_kiri)) !!}</div>
                    <br>
                    <div class="findings-text">{{ $record->catatan_tambahan }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table conclusion-table" style="margin-top: 10px;">
            <tr>
                <td style="width: 17%; vertical-align: top;"><b><u>Kesimpulan</u>:</b></td>
                <td style="width: 43%;"><b>{!! nl2br(e($record->kesimpulan)) !!}</b></td>
                <td style="width: 40%; text-align: center; border-bottom:0; border-top:0; border-right:0;">
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
            <tr>
                <td></td>
                <td></td>
                <td style="border-top:0; border-right:0;"></td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN KEDUA -->
    <div class="page-break page-2-layout">
        <div class="container">
            @include('pemeriksaan.partials.usg-mammae-patient-header')
        </div>
        <div class="image-container">
            @if ($record->gambar_hasil_usg)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_usg) }}"
                    alt="Hasil USG Mammae">
            @else
                <p>Gambar hasil USG Mammae tidak tersedia.</p>
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
