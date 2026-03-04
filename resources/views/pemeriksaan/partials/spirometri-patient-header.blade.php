<div class="report-title">HASIL PEMERIKSAAN SPIROMETRI</div>

<table class="info-table header-with-border">
    <tr>
        <td class="label">No. RM</td>
        <td class="separator">:</td>
        <td>{{ $record->no_rm }}</td>
        <td class="label">Instansi</td>
        <td class="separator">:</td>
        <td>{{ $record->instansi }}</td>
    </tr>
    <tr>
        <td class="label">Nama</td>
        <td class="separator">:</td>
        <td>{{ $record->participant?->name }}</td>
        <td class="label">Jenis Kelamin</td>
        <td class="separator">:</td>
        <td>{{ $record->participant?->gender }}</td>
    </tr>
    <tr>
        <td class="label">Tanggal Lahir</td>
        <td class="separator">:</td>
        <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->translatedFormat('j F Y') }}</td>
        <td class="label">Usia</td>
        <td class="separator">:</td>
        <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
    </tr>
    <tr>
        <td class="label">Pelaksanaan</td>
        <td class="separator">:</td>
        <td>{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->translatedFormat('j F Y') }}</td>
        <td class="value" colspan="3"></td>
    </tr>
</table>
