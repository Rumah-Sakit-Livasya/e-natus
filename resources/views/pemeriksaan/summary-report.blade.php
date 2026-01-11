<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Laporan Pemeriksaan - {{ $participant->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .container {
            width: 95%;
            margin: auto;
        }

        .main-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .patient-info {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 20px;
        }

        .patient-info table {
            width: 100%;
        }

        .report-section {
            border: 1px solid #ccc;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .report-section h2 {
            background-color: #e0e0e0;
            margin: 0;
            padding: 8px;
            font-size: 16px;
        }

        .report-content {
            padding: 10px;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-content th,
        .report-content td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .report-content th {
            background-color: #f7f7f7;
            text-align: left;
        }

        .label {
            font-weight: bold;
            width: 25%;
        }

        .findings-text {
            white-space: pre-wrap;
        }

        @media print {
            .report-section {
                border: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main-header">
            <h1>Rekapitulasi Laporan Pemeriksaan Medis</h1>
        </div>

        <div class="patient-info">
            <table>
                <tr>
                    <td><b>Nama Pasien</b></td>
                    <td>: {{ $participant->name }}</td>
                    <td><b>Jenis Kelamin</b></td>
                    <td>: {{ $participant->gender }}</td>
                </tr>
                <tr>
                    <td><b>No. Pegawai/NIK</b></td>
                    <td>: {{ $participant->employee_code }}</td>
                    <td><b>Departemen</b></td>
                    <td>: {{ $participant->department }}</td>
                </tr>
                <tr>
                    <td><b>Tanggal Lahir</b></td>
                    <td>: {{ \Carbon\Carbon::parse($participant->date_of_birth)->translatedFormat('j F Y') }} (Usia:
                        {{ \Carbon\Carbon::parse($participant->date_of_birth)->age }} thn)</td>
                    <td><b>Proyek</b></td>
                    <td>: {{ $participant->projectRequest?->name }}</td>
                </tr>
            </table>
        </div>

        {{-- Loop untuk setiap jenis pemeriksaan --}}

        @if ($participant->labChecks->isNotEmpty())
            <div class="report-section">
                <h2>HASIL PEMERIKSAAN LABORATORIUM LENGKAP</h2>
                @foreach ($participant->labChecks as $record)
                    <div class="report-content">
                        <b>Tanggal: {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }}
                            | No. Lab: {{ $record->no_lab }}</b>
                        <p><i>(Menampilkan ringkasan. Untuk detail nilai rujukan, lihat laporan asli)</i></p>
                        <table>
                            <tr>
                                <th colspan="2">Hematologi</th>
                            </tr>
                            <tr>
                                <td class="label">Hemoglobin</td>
                                <td>{{ $record->hemoglobin }} g/dL</td>
                            </tr>
                            <tr>
                                <td class="label">Leukosit</td>
                                <td>{{ $record->leukosit }} 10³/uL</td>
                            </tr>
                            <tr>
                                <td class="label">Trombosit</td>
                                <td>{{ $record->trombosit }} 10³/uL</td>
                            </tr>
                            <tr>
                                <th colspan="2">Fungsi Hati</th>
                            </tr>
                            <tr>
                                <td class="label">SGOT</td>
                                <td>{{ $record->sgot }} µ/L</td>
                            </tr>
                            <tr>
                                <td class="label">SGPT</td>
                                <td>{{ $record->sgpt }} µ/L</td>
                            </tr>
                            <tr>
                                <th colspan="2">Fungsi Ginjal</th>
                            </tr>
                            <tr>
                                <td class="label">Ureum</td>
                                <td>{{ $record->ureum }} mg/dL</td>
                            </tr>
                            <tr>
                                <td class="label">Kreatinin</td>
                                <td>{{ $record->kreatinin }} mg/dL</td>
                            </tr>
                            <tr>
                                <th colspan="2">Profil Lemak</th>
                            </tr>
                            <tr>
                                <td class="label">Kolesterol Total</td>
                                <td>{{ $record->kolesterol_total }} mg/dL</td>
                            </tr>
                            <tr>
                                <td class="label">Trigliserida</td>
                                <td>{{ $record->trigliserida }} mg/dL</td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($participant->rontgenChecks->isNotEmpty())
            <div class="report-section">
                <h2>HASIL PEMERIKSAAN RONTGEN</h2>
                @foreach ($participant->rontgenChecks as $record)
                    <div class="report-content">
                        <b>Tanggal:
                            {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }} | No.
                            Rontgen: {{ $record->no_rontgen }}</b>
                        <table>
                            <tr>
                                <td class="label">Temuan</td>
                                <td class="findings-text">{{ $record->temuan }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kesan</td>
                                <td><b>{{ $record->kesan }}</b></td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($participant->ekgChecks->isNotEmpty())
            <div class="report-section">
                <h2>HASIL PEMERIKSAAN EKG</h2>
                @foreach ($participant->ekgChecks as $record)
                    <div class="report-content">
                        <b>Tanggal:
                            {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }} | No.
                            RM: {{ $record->no_rm }}</b>
                        <table>
                            <tr>
                                <td class="label">Irama</td>
                                <td>{{ $record->irama }}</td>
                            </tr>
                            <tr>
                                <td class="label">Heart Rate</td>
                                <td>{{ $record->heart_rate }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kesan</td>
                                <td><b>{{ $record->kesimpulan }}</b></td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tambahkan blok @if lainnya untuk semua pemeriksaan (Spirometri, Treadmill, USG, dll.) dengan pola yang sama --}}

        @if ($participant->audiometryChecks->isNotEmpty())
            <div class="report-section">
                <h2>HASIL PEMERIKSAAN AUDIOMETRI</h2>
                @foreach ($participant->audiometryChecks as $record)
                    <div class="report-content">
                        <b>Tanggal:
                            {{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }} | No.
                            RM: {{ $record->no_rm }}</b>
                        <table>
                            <tr>
                                <td class="label">Ambang Dengar Kanan (AD)</td>
                                <td>{{ $record->derajat_ad }}</td>
                            </tr>
                            <tr>
                                <td class="label">Ambang Dengar Kiri (AS)</td>
                                <td>{{ $record->derajat_as }}</td>
                            </tr>
                            <tr>
                                <td class="label">Kesan</td>
                                <td><b>{{ $record->kesimpulan }}</b></td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <script>
        setTimeout(function() {
            window.print();
        }, 500);
    </script>
</body>

</html>
