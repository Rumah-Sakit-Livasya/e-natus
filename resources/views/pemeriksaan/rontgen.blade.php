<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Rontgen - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    <div class="container">
        <div class="report-title">HASIL PEMERIKSAAN RONTGEN</div>
        <div class="report-subtitle">( RADIOLOGI )</div>

        @include('pemeriksaan.partials.rontgen-patient-header')

        <table class="content-table border">
            <tr>
                <td class="no-border left-note"><span class="bold underline">Yth, TS.</span></td>
            </tr>
            <tr>
                <td>{!! nl2br(e($record->temuan)) !!}</td>
            </tr>
        </table>

        <table class="conclusion-table border mt-10">
            <tr>
                <td class="bold underline w-13">Kesan</td>
                <td class="center w-2">:</td>
                <td>{{ $record->kesan }}</td>
                <td class="center w-35">
                    Radiologist
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ Illuminate\Support\Facades\Storage::url($record->tanda_tangan) }}" class="ttd-image"
                                alt="TTD">
                        @endif
                    </div>
                    <div class="doctor-name">{{ $record->radiologist }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break page-2-layout print-page">
        <div class="container">
            @include('pemeriksaan.partials.rontgen-patient-header')
        </div>
        <div class="image-container">
            @if ($record->gambar_hasil_rontgen)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_rontgen) }}" alt="Hasil Rontgen">
            @else
                <div class="empty-image">Gambar hasil rontgen tidak tersedia.</div>
            @endif
        </div>
    </div>
</body>

</html>
