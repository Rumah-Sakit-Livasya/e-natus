<table>
    <thead>
        <tr>
            <th>id</th>
            <th>nama_bmhp</th>
            <th>satuan</th>
            <th>pcs_per_unit</th>
            <th>stok_awal</th>
            <th>stok_sisa</th>
            <th>stok_minimum</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['id'] ?? '' }}</td>
            <td>{{ $row['nama_bmhp'] }}</td>
            <td>{{ $row['satuan'] }}</td>
            <td>{{ $row['pcs_per_unit'] ?? '' }}</td>
            <td>{{ $row['stok_awal'] }}</td>
            <td>{{ $row['stok_sisa'] }}</td>
            <td>{{ $row['stok_minimum'] }}</td>
        </tr>
        @endforeach

        {{-- Baris kosong untuk tambah data baru via import --}}
        <tr>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
