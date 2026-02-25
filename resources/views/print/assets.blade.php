<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Semua Aset</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #3498db;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        .header .subtitle {
            margin: 0.5rem 0 0 0;
            font-size: 14px;
            color: #7f8c8d;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 12px;
        }

        .info-item {
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #7f8c8d;
        }

        .table-container {
            margin-top: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            font-size: 11px;
        }

        thead {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background-color 0.2s ease;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e3f2fd;
        }

        td {
            padding: 10px 8px;
            border: none;
            vertical-align: middle;
        }

        td:first-child {
            font-weight: 600;
            color: #3498db;
            text-align: center;
            width: 40px;
        }

        .asset-name {
            font-weight: 500;
            color: #2c3e50;
        }

        .asset-code {
            font-family: 'Courier New', monospace;
            background: #f1f3f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            display: inline-block;
        }

        .year-badge {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
        }

        .footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
        }

        @media print {
            body {
                font-size: 10px;
            }

            .header h1 {
                font-size: 18px;
            }

            .info-section {
                font-size: 9px;
            }

            table {
                font-size: 9px;
            }

            th {
                font-size: 10px;
                padding: 8px 4px;
            }

            td {
                padding: 6px 4px;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => window.close(), 500);
        };
    </script>
</head>

<body>
    <div class="header">
        <h1>LAPORAN DAFTAR ASET</h1>
        <div class="subtitle">Rumah Sakit Livasya</div>
    </div>

    <div class="info-section">
        <div class="info-item">
            <div class="info-label">Tanggal Cetak</div>
            <div class="info-value">{{ date('d F Y') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Total Aset</div>
            <div class="info-value">{{ $assets->count() }} Unit</div>
        </div>
        <div class="info-item">
            <div class="info-label">Periode</div>
            <div class="info-value">Semua Data</div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Aset</th>
                    <th>Kode Inventaris</th>
                    <th>Tahun Perolehan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $i => $asset)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="asset-name">{{ $asset->custom_name }}</td>
                        <td><span class="asset-code">{{ $asset->code }}</span></td>
                        <td><span class="year-badge">{{ $asset->purchase_year }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>Dokumen ini dicetak secara otomatis dari Sistem E-Natus</div>
        <div>© {{ date('Y') }} Rumah Sakit Livasya - Sistem Informasi Manajemen</div>
    </div>
</body>

</html>
