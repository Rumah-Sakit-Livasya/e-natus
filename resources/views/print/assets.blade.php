<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Semua Aset</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 12px;
        }

        th {
            background-color: #f3f3f3;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
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
    <h2>Daftar Seluruh Aset</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                {{-- <th>Template</th>
                <th>Kategori</th> --}}
                <th>Nama Aset</th>
                <th>Kode</th>
                {{-- <th>Kondisi</th>
                <th>Merk</th> --}}
                <th>Tahun</th>
                {{-- <th>Tarif</th>
                <th>Satuan</th>
                <th>Status</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $i => $asset)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    {{-- <td>{{ $asset->template->name ?? '-' }}</td> --}}
                    {{-- <td>{{ $asset->template->category->name ?? '-' }}</td> --}}
                    <td>{{ $asset->custom_name }}</td>
                    <td>{{ $asset->code }}</td>
                    {{-- <td>{{ ucfirst($asset->condition) }}</td>
                    <td>{{ $asset->brand }}</td> --}}
                    <td>{{ $asset->purchase_year }}</td>
                    {{-- <td>Rp {{ number_format($asset->tarif, 0, ',', '.') }}</td>
                    <td>{{ $asset->satuan }}</td>
                    <td>{{ $asset->status == 'available' ? 'Tersedia' : 'Tidak Tersedia' . 'Dipakai di: ' . $asset->projectRequests()->pluck('project.name')->implode(', ') }}
                    </td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
