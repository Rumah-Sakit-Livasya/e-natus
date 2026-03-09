<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Treadmill - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <link rel="stylesheet" href="{{ asset('css/treadmill-print.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js" defer></script>
    <script src="{{ asset('js/treadmill-print.js') }}" defer></script>
</head>

<body class="print-page">
    @php
        $attachmentPath = $record->gambar_hasil_treadmill;
        $attachmentUrl = $attachmentPath ? Illuminate\Support\Facades\Storage::url($attachmentPath) : null;
        $isPdfAttachment = $attachmentPath && Illuminate\Support\Str::endsWith(strtolower($attachmentPath), '.pdf');
    @endphp

    <div id="treadmill-payload" data-attachment-url="{{ $attachmentUrl }}" data-is-pdf="{{ $isPdfAttachment ? '1' : '0' }}"></div>

    <div class="container">
        <div class="report-title">HASIL PEMERIKSAAN TREADMILL</div>
        <div class="report-subtitle">( TREADMILL TEST )</div>

        @include('pemeriksaan.partials.treadmill-patient-header')

        <table class="content-table border treadmill-table">
            <tr>
                <td class="label">Metode</td>
                <td class="separator">:</td>
                <td>{{ $record->metode }}</td>
            </tr>
            <tr>
                <td class="label">EKG Resting</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_resting }}</td>
            </tr>
            <tr>
                <td class="label">EKG Exercise</td>
                <td class="separator">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label sub-label">ST-T Change Segmen</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_exercise_st_change }}</td>
            </tr>
            <tr>
                <td class="label sub-label">Aritmia</td>
                <td class="separator">:</td>
                <td>{{ $record->ekg_exercise_aritmia }}</td>
            </tr>
            <tr>
                <td class="label">Tekanan Darah</td>
                <td class="separator">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label sub-label">Awal</td>
                <td class="separator">:</td>
                <td>{{ $record->td_awal }} mm/Hg</td>
            </tr>
            <tr>
                <td class="label sub-label">Tertinggi</td>
                <td class="separator">:</td>
                <td>{{ $record->td_tertinggi }} mm/Hg</td>
            </tr>
            <tr>
                <td class="label">Indikasi Berhenti</td>
                <td class="separator">:</td>
                <td>{{ $record->indikasi_berhenti }}</td>
            </tr>
            <tr>
                <td class="label">Target HR Tercapai</td>
                <td class="separator">:</td>
                <td>{{ $record->target_hr }} : {{ $record->tercapai_hr }}</td>
            </tr>
            <tr>
                <td class="label">Lama Test</td>
                <td class="separator">:</td>
                <td>{{ $record->lama_tes_menit }} Menit {{ $record->lama_tes_detik }} Detik</td>
            </tr>
            <tr class="divider-row">
                <td colspan="3"></td>
            </tr>
            <tr>
                <td class="label">Kapasitas Aerobik</td>
                <td class="separator">:</td>
                <td>{{ $record->kapasitas_aerobik }} Mets</td>
            </tr>
            <tr>
                <td class="label">Kelas Fungsional</td>
                <td class="separator">:</td>
                <td>{{ $record->kelas_fungsional }}</td>
            </tr>
            <tr>
                <td class="label">Tingkat Kebugaran</td>
                <td class="separator">:</td>
                <td>{{ $record->tingkat_kebugaran }}</td>
            </tr>
            <tr class="divider-row">
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="3" class="result-section-cell">
                    <div class="result-grid">
                        <div>
                            <div><span class="bold underline">Kesimpulan</span> : {{ $record->kesimpulan }}</div>
                            <div class="mt-10"><span class="bold underline">Saran</span> :</div>
                            <div class="preserve-line">{{ $record->saran }}</div>
                        </div>
                        <div class="center">
                            Cardiologist
                            <div class="signature-area">
                                @if ($record->tanda_tangan)
                                    <img src="{{ \App\Support\StoragePublicUrl::fromPath($record->tanda_tangan) }}" class="ttd-image"
                                        alt="TTD">
                                @endif
                            </div>
                            <div class="doctor-name">{{ $record->cardiologist }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if ($attachmentUrl)
        <div class="page-break print-page treadmill-attachment-wrapper">
            <div class="container">
                @include('pemeriksaan.partials.treadmill-patient-header')

                <div class="attachment-title">Lampiran Hasil Treadmill</div>

                @if (! $isPdfAttachment)
                    <div class="image-container">
                        <img src="{{ $attachmentUrl }}" alt="Lampiran Hasil Treadmill">
                    </div>
                @else
                    <div id="treadmill-attachment-pages" class="attachment-pages"></div>
                    <div id="treadmill-attachment-fallback" class="empty-image hidden">
                        Lampiran PDF tidak dapat ditampilkan.
                    </div>
                @endif
            </div>
        </div>
    @endif
</body>

</html>
