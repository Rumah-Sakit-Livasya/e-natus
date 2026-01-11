{{-- resources/views/print/partials/spirometry_patient_header.blade.php --}}

<div class="header">HASIL PEMERIKSAAN SPIROMETRI</div>

<table class="info-table">
    <tr>
        <td class="label">No. RM</td>
        <td class="separator">:</td>
        <td>{{ $record->no_rm }}</td>
        <td></td>
        <td></td>
        <td></td>
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
        <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d-m-Y') }}</td>
        <td class="label">Instansi</td>
        <td class="separator">:</td>
        <td>{{ $record->instansi }}</td>
    </tr>
    <tr>
        <td class="label">Usia</td>
        <td class="separator">:</td>
        <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
        <td class="label">Pelaksanaan</td>
        <td class="separator">:</td>
        <td>{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
    </tr>
</table>
