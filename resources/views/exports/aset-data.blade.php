<table>
    <thead>
        <tr>
            <th>id</th>
            <th>template_name</th>
            <th>lander_name</th>
            <th>custom_name</th>
            <th>type</th>
            <th>serial_number</th>
            <th>code</th>
            <th>condition</th>
            <th>brand</th>
            <th>purchase_year</th>
            <th>tarif</th>
            <th>harga_sewa</th>
            <th>satuan</th>
            <th>status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['id'] ?? '' }}</td>
                <td>{{ $row['template_name'] ?? '' }}</td>
                <td>{{ $row['lander_name'] ?? '' }}</td>
                <td>{{ $row['custom_name'] ?? '' }}</td>
                <td>{{ $row['type'] ?? '' }}</td>
                <td>{{ $row['serial_number'] ?? '' }}</td>
                <td>{{ $row['code'] ?? '' }}</td>
                <td>{{ $row['condition'] ?? '' }}</td>
                <td>{{ $row['brand'] ?? '' }}</td>
                <td>{{ $row['purchase_year'] ?? '' }}</td>
                <td>{{ $row['tarif'] ?? '' }}</td>
                <td>{{ $row['harga_sewa'] ?? '' }}</td>
                <td>{{ $row['satuan'] ?? '' }}</td>
                <td>{{ $row['status'] ?? '' }}</td>
            </tr>
        @endforeach

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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

