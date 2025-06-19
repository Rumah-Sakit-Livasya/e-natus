<div class="overflow-x-auto py-2">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead>
            <tr>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Nama Barang</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Unit</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Harga</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Qty</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Satuan</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Jumlah</th>
                <th class="text-left px-4 py-2 font-semibold text-gray-700">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($items as $item)
                <tr wire:key="procurement-item-{{ $item['id'] }}">
                    <td class="px-4 py-2">{{ $item['nama_barang'] }}</td>
                    <td class="px-4 py-2">{{ $item['unit'] }}</td>
                    <td class="px-4 py-2">Rp{{ number_format($item['harga_pengajuan'], 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $item['qty_pengajuan'] }}</td>
                    <td class="px-4 py-2">{{ $item['satuan'] }}</td>
                    <td class="px-4 py-2">
                        Rp{{ number_format($item['harga_pengajuan'] * $item['qty_pengajuan'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2">
                        <span
                            class="inline-block px-2 py-1 rounded text-xs font-medium
                            {{ $item['status'] === 'Terealisasi' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $item['status'] === 'Tidak Terealisasi' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $item['status'] === null ? 'bg-gray-100 text-gray-500' : '' }}">
                            {{ $item['status'] ?? 'Belum Ditentukan' }}
                        </span>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-2 text-gray-500 italic">
                        Tidak ada item yang terhubung.
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
</div>
