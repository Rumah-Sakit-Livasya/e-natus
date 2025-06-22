<div class="space-y-4 text-sm text-gray-700 dark:text-gray-200">
    <div>
        <h2 class="text-lg font-bold uppercase">RENCANA ANGGARAN BIAYA</h2>
        <div class="mt-2">
            <div><strong>Lokasi:</strong> {{ $project->lokasi }}</div>
            <div><strong>Perusahaan:</strong> {{ $project->client->name ?? '-' }}</div>
            <div>
                <strong>Tanggal MCU:</strong>
                {{ \Carbon\Carbon::parse($project->start_period)->translatedFormat('d') }}
                &ndash;
                {{ \Carbon\Carbon::parse($project->end_period)->translatedFormat('d F Y') }}
                ({{ \Carbon\Carbon::parse($project->start_period)->diffInDays(\Carbon\Carbon::parse($project->end_period)) + 1 }}
                HARI)
            </div>
            <div><strong>Estimasi:</strong> {{ $project->jumlah }} peserta</div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table
            class="w-full text-sm text-left border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-2 w-8">No</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2 text-right">Price</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($rows as $index => $item)
                    <tr>
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $item->description }}</td>
                        <td class="px-4 py-2 text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                <tr>
                    <td colspan="2" class="px-4 py-2 text-right">Total</td>
                    <td class="px-4 py-2 text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="px-4 py-2 text-right">Nilai Invoice</td>
                    <td class="px-4 py-2 text-right text-green-600 dark:text-green-400">Rp
                        {{ number_format($nilaiInvoice, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="px-4 py-2 text-right">Margin</td>
                    <td class="px-4 py-2 text-right text-blue-600 dark:text-blue-400">Rp
                        {{ number_format($margin, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
