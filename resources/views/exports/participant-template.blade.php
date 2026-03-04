<table>
    <thead>
        <tr>
            <th>id</th>
            <th>project_request_id</th>
            <th>nama_proyek</th>
            <th>name</th>
            <th>employee_code</th>
            <th>department</th>
            <th>date_of_birth</th>
            <th>address</th>
            <th>gender</th>
            <th>marital_status</th>
            <th>note</th>
        </tr>
    </thead>
    <tbody>
        {{-- Baris contoh (baris ke-2): tidak akan diimport --}}
        <tr>
            <td></td>
            <td>{{ $project?->id ?? '' }}</td>
            <td>{{ $project?->name ?? '' }}</td>
            <td>Nama Peserta</td>
            <td>123456</td>
            <td>Instansi</td>
            <td>1990-01-01</td>
            <td>Alamat lengkap</td>
            <td>Laki-laki</td>
            <td>Belum Menikah</td>
            <td></td>
        </tr>

        @foreach($rows as $row)
        <tr>
            <td>{{ $row['id'] ?? '' }}</td>
            <td>{{ $row['project_request_id'] ?? ($project?->id ?? '') }}</td>
            <td>{{ $row['nama_proyek'] ?? ($project?->name ?? '') }}</td>
            <td>{{ $row['name'] ?? '' }}</td>
            <td>{{ $row['employee_code'] ?? '' }}</td>
            <td>{{ $row['department'] ?? '' }}</td>
            <td>{{ $row['date_of_birth'] ?? '' }}</td>
            <td>{{ $row['address'] ?? '' }}</td>
            <td>{{ $row['gender'] ?? '' }}</td>
            <td>{{ $row['marital_status'] ?? '' }}</td>
            <td>{{ $row['note'] ?? '' }}</td>
        </tr>
        @endforeach

        <tr>
            <td></td>
            <td>{{ $project?->id ?? '' }}</td>
            <td>{{ $project?->name ?? '' }}</td>
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
            <td>{{ $project?->id ?? '' }}</td>
            <td>{{ $project?->name ?? '' }}</td>
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
            <td>{{ $project?->id ?? '' }}</td>
            <td>{{ $project?->name ?? '' }}</td>
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
