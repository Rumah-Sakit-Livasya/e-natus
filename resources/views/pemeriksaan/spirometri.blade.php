<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Spirometri - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    <div class="container">
        @include('pemeriksaan.partials.spirometri-patient-header')

        <table class="result-table mt-10">
            <thead>
                <tr>
                    <th>Pemeriksaan</th>
                    <th>Nilai</th>
                    <th>Prediksi</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Best VC (ml)</td>
                    <td>{{ $record->vc_nilai }}</td>
                    <td>{{ $record->vc_prediksi }}</td>
                    <td>{{ $record->vc_prediksi > 0 ? number_format(($record->vc_nilai / $record->vc_prediksi) * 100, 2) . ' %' : '' }}</td>
                </tr>
                <tr>
                    <td>Best FVC (ml)</td>
                    <td>{{ $record->fvc_nilai }}</td>
                    <td>{{ $record->fvc_prediksi }}</td>
                    <td>{{ $record->fvc_prediksi > 0 ? number_format(($record->fvc_nilai / $record->fvc_prediksi) * 100, 2) . ' %' : '' }}</td>
                </tr>
                <tr>
                    <td>Best FEV1 (ml)</td>
                    <td>{{ $record->fev1_nilai }}</td>
                    <td>{{ $record->fev1_prediksi }}</td>
                    <td>{{ $record->fev1_prediksi > 0 ? number_format(($record->fev1_nilai / $record->fev1_prediksi) * 100, 2) . ' %' : '' }}</td>
                </tr>
                <tr>
                    <td>FEV1 / FVC (%)</td>
                    <td>{{ $record->fvc_nilai > 0 ? number_format($record->fev1_nilai / $record->fvc_nilai, 4) : '' }}</td>
                    <td>{{ $record->fvc_prediksi > 0 ? number_format($record->fev1_prediksi / $record->fvc_prediksi, 4) : '' }}</td>
                    <td>
                        @php
                            $nilaiRatio = $record->fvc_nilai > 0 ? $record->fev1_nilai / $record->fvc_nilai : 0;
                            $predRatio = $record->fvc_prediksi > 0 ? $record->fev1_prediksi / $record->fvc_prediksi : 0;
                        @endphp
                        {{ $predRatio > 0 ? number_format(($nilaiRatio / $predRatio) * 100, 2) . ' %' : '' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="info-table no-inner mt-10">
            <tr>
                <td class="label bold">Kesan</td>
                <td class="separator">:</td>
                <td>{{ $record->kesan }}</td>
            </tr>
            <tr>
                <td class="label bold">Saran</td>
                <td class="separator">:</td>
                <td>{{ $record->saran }}</td>
            </tr>
        </table>

        <table class="info-table no-inner mt-20">
            <tr>
                <td class="w-60"></td>
                <td class="center">
                    Dokter Pemeriksa,
                    <div class="signature-area">
                        @if ($record->tanda_tangan)
                            <img src="{{ \App\Support\StoragePublicUrl::fromPath($record->tanda_tangan) }}" class="ttd-image" alt="TTD">
                        @endif
                    </div>
                    <div class="doctor-name">{{ $record->dokter_pemeriksa }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break page-2-layout print-page">
        <div class="container">
            @include('pemeriksaan.partials.spirometri-patient-header')
        </div>

        <div class="image-container">
            @if ($record->gambar_hasil_spirometri)
                <img src="{{ Illuminate\Support\Facades\Storage::url($record->gambar_hasil_spirometri) }}"
                    alt="Hasil Spirometri">
            @else
                <div class="empty-image">Gambar hasil spirometri tidak tersedia.</div>
            @endif
        </div>
    </div>
</body>

</html>
