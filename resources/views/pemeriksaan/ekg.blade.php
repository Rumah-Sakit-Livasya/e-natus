<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan EKG - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    <div class="container">
        @include('pemeriksaan.partials.ekg-patient-header')

        <table class="content-table border">
            <tr class="divider-row">
                <td colspan="6"></td>
            </tr>
            <tr>
                <td class="label">Irama</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->irama }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">Heart Rate</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->heart_rate }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">Axis</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->axis }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">PR Interval</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->pr_interval }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">QRS Duration</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->qrs_duration }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">Gel T</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->gel_t }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">ST-T Changes</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->st_t_changes }}</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">Kelainan</td>
                <td class="separator">:</td>
                <td class="value">{{ $record->kelainan }}</td>
                <td colspan="3"></td>
            </tr>
            <tr class="divider-row">
                <td colspan="6"></td>
            </tr>
            <tr>
                <td class="bold">Kesimpulan</td>
                <td class="separator">:</td>
                <td class="bold">{{ $record->kesimpulan }}</td>
                <td colspan="3" class="center">
                    Cardiologist
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}" class="ttd-image"
                                alt="Tanda Tangan">
                        @endif
                    </div>
                    <div class="doctor-name">{{ $record->dokter_pemeriksa }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break page-2-layout print-page">
        <div class="container">
            @include('pemeriksaan.partials.ekg-patient-header')
        </div>

        <div class="image-container">
            @if ($record->gambar_hasil_ekg)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_ekg) }}" alt="Hasil EKG">
            @else
                <div class="empty-image">Gambar hasil EKG tidak tersedia.</div>
            @endif
        </div>
    </div>
</body>

</html>
