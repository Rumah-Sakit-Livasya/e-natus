@php
    $record = $state;
@endphp

<div class="overflow-x-auto py-2">
    <table class="min-w-full text-sm text-left whitespace-nowrap">
        <thead class="bg-gray-100 font-semibold text-gray-700">
            <tr>
                <th>#</th>
                <th>Nama Barang</th>
                <th>Unit</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->items as $i => $item)
                <tr class="border-t">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>Rp{{ number_format($item->harga_pengajuan, 0, ',', '.') }}</td>
                    <td>{{ $item->qty_pengajuan }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>Rp{{ number_format($item->harga_pengajuan * $item->qty_pengajuan, 0, ',', '.') }}</td>
                    <td>
                        <span
                            class="inline-flex px-2 py-1 text-xs font-medium rounded-md
                            {{ $item->status === 'Terealisasi'
                                ? 'bg-green-100 text-green-700'
                                : ($item->status === 'Tidak Terealisasi'
                                    ? 'bg-red-100 text-red-700'
                                    : 'bg-gray-100 text-gray-600') }}">
                            {{ $item->status ?? 'Belum Ditentukan' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
