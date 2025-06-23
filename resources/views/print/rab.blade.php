<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak RAB - {{ $project->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => window.close(), 500);
        }
    </script>
</head>

<body class="p-6 text-sm text-gray-800">
    <img src="{{ asset('img/kop.png') }}" alt="">

    <h2 class="text-xl font-bold uppercase mb-4">RENCANA ANGGARAN BIAYA</h2>

    <div class="mb-4">
        <div><strong>Lokasi:</strong> {{ $project->lokasi }}</div>
        <div><strong>Perusahaan:</strong> {{ $project->client->name ?? '-' }}</div>
        <div>
            <strong>Tanggal MCU:</strong>
            {{ \Carbon\Carbon::parse($project->start_period)->translatedFormat('d') }} –
            {{ \Carbon\Carbon::parse($project->end_period)->translatedFormat('d F Y') }}
            ({{ \Carbon\Carbon::parse($project->start_period)->diffInDays($project->end_period) + 1 }} HARI)
        </div>
        <div><strong>Estimasi:</strong> {{ $project->jumlah }} peserta</div>
    </div>

    <table class="w-full border border-gray-400">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-2 border">No</th>
                <th class="px-4 py-2 border">Description</th>
                <th class="px-4 py-2 border text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $item)
                <tr>
                    <td class="px-4 py-2 border">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $item->description }}</td>
                    <td class="px-4 py-2 border text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="font-semibold">
            <tr>
                <td colspan="2" class="px-4 py-2 border text-right">Total</td>
                <td class="px-4 py-2 border text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="px-4 py-2 border text-right">Nilai Invoice</td>
                <td class="px-4 py-2 border text-right text-green-700">Rp
                    {{ number_format($project->nilai_invoice, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="px-4 py-2 border text-right">Margin</td>
                <td class="px-4 py-2 border text-right text-blue-700">Rp {{ number_format($margin, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
