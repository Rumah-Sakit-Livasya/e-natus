<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pemeriksaan Laboratorium - {{ $record->participant?->name }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .header,
        .content {
            border: 1px solid black;
            width: 100%;
            border-collapse: collapse;
        }

        .header td,
        .content td {
            border: 1px solid black;
            padding: 5px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            padding: 10px 0;
        }

        /* ======================================================= */
        /*             PERUBAHAN WARNA ADA DI DUA BARIS INI        */
        /* ======================================================= */
        .section-title {
            background-color: #b4d5fd;
            font-weight: bold;
            text-align: center;
            padding: 8px;
        }

        /* <-- Biru lebih gelap */
        .sub-section-title {
            background-color: #dce9f9;
            font-weight: bold;
            padding: 6px;
        }

        /* <-- Biru lebih terang */

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .result-table td,
        .result-table th {
            border: 1px solid black;
            padding: 5px;
        }

        .result-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .signature {
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">PEMERIKSAAN LABORATORIUM</div>
        <table class="header">
            <tr>
                <td>No.NIK/Nama</td>
                <td colspan="3">{{ $record->participant?->employee_code }} / {{ $record->participant?->name }}</td>
                <td>Tgl.Lahir</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d M Y') }}</td>
                <td>UMUR</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Thn</td>
            </tr>
            <tr>
                <td>PT/DEPT</td>
                <td colspan="3">{{ $record->department }}</td>
                <td>J.KEL</td>
                <td>{{ $record->participant?->gender }}</td>
                <td>No.MCU</td>
                <td>{{ $record->no_mcu }}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right; font-weight: bold;">
                    Tanggal Pemeriksaan :
                    {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->isoFormat('DD-MM-YYYY') }}
                </td>
            </tr>
        </table>

        <br>

        <table class="content">
            <tr class="section-title">
                <td colspan="3">HASIL PEMERIKSAAN LABORATORIUM</td>
            </tr>
            <tr class="sub-section-title">
                <td colspan="3">Hasil Pemeriksaan Tes Narkoba</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 10px;">
                    Telah melakukan pemeriksaan Narkoba pada tanggal :
                    {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->isoFormat('DD MMMM YYYY') }} <br>
                    Dengan hasil sebagai berikut,
                    <table class="result-table">
                        <tr>
                            <th>Pemeriksaan</th>
                            <th>Hasil</th>
                            <th>Pemeriksaan</th>
                        </tr>
                        <tr>
                            <td>Amphetamine</td>
                            <td>{{ $record->amphetamine }}</td>
                            <td>[ {{ $record->amphetamine }} ]</td>
                        </tr>
                        <tr>
                            <td>Metamphetamine</td>
                            <td>{{ $record->metamphetamine }}</td>
                            <td>[ {{ $record->metamphetamine }} ]</td>
                        </tr>
                        <tr>
                            <td>Cocaine</td>
                            <td>{{ $record->cocaine }}</td>
                            <td>[ {{ $record->cocaine }} ]</td>
                        </tr>
                        <tr>
                            <td>THC</td>
                            <td>{{ $record->thc }}</td>
                            <td>[ {{ $record->thc }} ]</td>
                        </tr>
                        <tr>
                            <td>Morphine</td>
                            <td>{{ $record->morphine }}</td>
                            <td>[ {{ $record->morphine }} ]</td>
                        </tr>
                        <tr>
                            <td>Benzodiazepine</td>
                            <td>{{ $record->benzodiazepine }}</td>
                            <td>[ {{ $record->benzodiazepine }} ]</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 10px;">Demikian disampaikan dan diucapkan terima kasih.</td>
            </tr>
        </table>

        <div class="footer">
            Analis Kesehatan,
            <div class="signature">
                <u>{{ $record->analis_kesehatan }}</u>
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
