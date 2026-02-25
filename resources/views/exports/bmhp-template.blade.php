<table>
    <thead>
        <tr>
            <th>nama_bmhp</th>
            <th>satuan</th>
            <th>stok_awal</th>
            <th>stok_sisa</th>
            <th>stok_minimum</th>
        </tr>
    </thead>
    <tbody>
        {{-- Sample data untuk panduan --}}
        @foreach($sampleData as $row)
        <tr>
            <td>{{ $row['nama_bmhp'] }}</td>
            <td>{{ $row['satuan'] }}</td>
            <td>{{ $row['stok_awal'] }}</td>
            <td>{{ $row['stok_sisa'] }}</td>
            <td>{{ $row['stok_minimum'] }}</td>
        </tr>
        @endforeach

        {{-- Baris kosong untuk data baru --}}
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
