<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil USG Abdomen - {{ $record->participant?->name }}</title>
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

        .content-table td {
            padding: 8px 6px;
            vertical-align: top;
            border: 1px solid #555;
        }

        .content-table .label {
            width: 20%;
            font-weight: bold;
            text-decoration: underline;
        }

        .content-table .separator {
            width: 2%;
        }

        .findings-text {
            white-space: pre-wrap;
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
        @include('pemeriksaan.partials.usg-patient-header')

        <table class="content-table">
            <tr>
                <td colspan="3" style="border-left: 0; border-right:0; border-top:0;">Telah dilakukan pemeriksaan USG
                    Abdomen, dengan hasil:</td>
            </tr>
            <tr>
                <td class="label">Hepar</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->hepar }}</td>
            </tr>
            <tr>
                <td class="label">Gallbladder</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->gallbladder }}</td>
            </tr>
            <tr>
                <td class="label">Lien</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->lien }}</td>
            </tr>
            <tr>
                <td class="label">Pankreas</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->pankreas }}</td>
            </tr>
            <tr>
                <td class="label">Ren Kanan</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->ren_kanan }}</td>
            </tr>
            <tr>
                <td class="label">Ren Kiri</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->ren_kiri }}</td>
            </tr>
            <tr>
                <td class="label">Vesica Urinaria</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->vesica_urinaria }}</td>
            </tr>
            <tr>
                <td class="label">Prostat</td>
                <td class="separator">:</td>
                <td class="findings-text">{{ $record->prostat }}</td>
            </tr>
            <tr>
                <td colspan="3" class="findings-text" style="padding-top: 15px;">{{ $record->catatan_tambahan_1 }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="findings-text">{{ $record->catatan_tambahan_2 }}</td>
            </tr>
        </table>

        <table class="info-table conclusion-table" style="margin-top: 10px;">
            <tr>
                <td style="width: 17%;"><b><u>Kesimpulan</u></b></td>
                <td style="width: 2%;">:</td>
                <td><b>{{ $record->kesimpulan }}</b></td>
                <td style="width: 40%; text-align: center; border-bottom:0; border-top:0;">
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
                <td colspan="3"></td>
                <td style="border-top:0;"></td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN KEDUA -->
    <div class="page-break page-2-layout">
        <div class="container">
            @include('pemeriksaan.partials.usg-patient-header')
        </div>
        <div class="image-container">
            @if ($record->gambar_hasil_usg)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_usg) }}" alt="Hasil USG">
            @else
                <p>Gambar hasil USG tidak tersedia.</p>
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
