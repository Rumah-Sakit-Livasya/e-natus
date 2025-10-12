<div class="header">
    <h2>HASIL PEMERIKSAAN EKG</h2>
    <h3>( ELEKTROKARDIOGRAM )</h3>
</div>

<table class="content-table" style="border: 2px solid black; margin-bottom: 10px;">
    <!-- Baris Informasi Pasien -->
    <tr>
        <td class="label">No. RM</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->no_rm }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="label">Nama</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->participant?->name }}</td>
        <td class="label">Jenis Kelamin</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->participant?->gender }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Lahir</td>
        <td class="separator">:</td>
        <td class="value">{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->translatedFormat('j F Y') }}
        </td>
        <td class="label">Instansi</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->instansi }}</td>
    </tr>
    <tr>
        <td class="label">Usia</td>
        <td class="separator">:</td>
        <td class="value">{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
        <td class="label">Pelaksanaan</td>
        <td class="separator">:</td>
        <td class="value">{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }}</td>
    </tr>
</table>
