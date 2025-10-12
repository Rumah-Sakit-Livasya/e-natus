{{-- resources/views/print/partials/rontgen_patient_header.blade.php --}}
<table class="info-table" style="border: 2px solid black; margin-bottom: 10px;">
    <tr>
        <td class="label">No. Rontgen</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->no_rontgen }}</td>
        <td class="label">No. RM</td>
        <td class="separator">:</td>
        <td class="value">{{ $record->no_rm }}</td>
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
        <td class="value">{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d-m-Y') }}</td>
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
        <td class="value">{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
    </tr>
</table>
