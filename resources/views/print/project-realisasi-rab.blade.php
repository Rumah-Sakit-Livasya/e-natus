<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Realisasi RAB - {{ $project->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background-color: #fafafa;
        }

        .danger {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body onload="window.print(); setTimeout(() => window.close(), 1000)">
    <h2>Realisasi RAB Proyek</h2>
    <p><strong>Nama Proyek:</strong> {{ $project->name }}</p>
    <p><strong>Klien:</strong> {{ $project->client->name ?? '-' }}</p>
    <p><strong>Periode:</strong> {{ $project->start_period }} s.d. {{ $project->end_period }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total Realisasi</th>
                <th>Total Rencana</th>
                <th>Selisih</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotalRealisasi = 0;
                $grandTotalRencana = 0;
            @endphp

            @foreach ($realisasi as $i => $item)
                @php
                    $rencana = $item->rabItem?->total ?? 0;
                    $selisih = $rencana - $item->total;
                    $grandTotalRealisasi += $item->total;
                    $grandTotalRencana += $rencana;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($rencana, 0, ',', '.') }}</td>
                    <td class="text-right {{ $selisih < 0 ? 'danger' : 'success' }}">
                        Rp {{ number_format($selisih, 0, ',', '.') }}
                    </td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($grandTotalRealisasi, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($grandTotalRencana, 0, ',', '.') }}</td>
                <td class="text-right {{ $grandTotalRencana - $grandTotalRealisasi < 0 ? 'danger' : 'success' }}">
                    Rp {{ number_format($grandTotalRencana - $grandTotalRealisasi, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
