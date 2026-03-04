<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil USG Abdomen - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    <div class="container">
        @include('pemeriksaan.partials.usg-patient-header')

        <table class="content-table border usg-findings">
            <tr>
                <td colspan="3" class="no-border">Telah dilakukan pemeriksaan USG Abdomen, dengan hasil:</td>
            </tr>
            <tr>
                <td>Hepar</td>
                <td>:</td>
                <td>{{ $record->hepar }}</td>
            </tr>
            <tr>
                <td>Gallbladder</td>
                <td>:</td>
                <td>{{ $record->gallbladder }}</td>
            </tr>
            <tr>
                <td>Lien</td>
                <td>:</td>
                <td>{{ $record->lien }}</td>
            </tr>
            <tr>
                <td>Pankreas</td>
                <td>:</td>
                <td>{{ $record->pankreas }}</td>
            </tr>
            <tr>
                <td>Ren Kanan</td>
                <td>:</td>
                <td>{{ $record->ren_kanan }}</td>
            </tr>
            <tr>
                <td>Ren Kiri</td>
                <td>:</td>
                <td>{{ $record->ren_kiri }}</td>
            </tr>
            <tr>
                <td>Vesica Urinaria</td>
                <td>:</td>
                <td>{{ $record->vesica_urinaria }}</td>
            </tr>
            <tr>
                <td>Prostat</td>
                <td>:</td>
                <td>{{ $record->prostat }}</td>
            </tr>
            <tr>
                <td colspan="3" class="no-border">{{ $record->catatan_tambahan_1 }}</td>
            </tr>
            <tr>
                <td colspan="3" class="no-border">{{ $record->catatan_tambahan_2 }}</td>
            </tr>
        </table>

        <table class="conclusion-table border mt-10">
            <tr>
                <td class="bold underline w-15">Kesimpulan</td>
                <td class="center w-2">:</td>
                <td>{{ $record->kesimpulan }}</td>
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
            @include('pemeriksaan.partials.usg-patient-header')
            @php
                $images = collect($record->gambar_hasil_usg_lampiran ?? [])
                    ->merge([
                    $record->gambar_hasil_usg ?? null,
                    $record->gambar_hasil_usg_2 ?? null,
                    $record->gambar_hasil_usg_3 ?? null,
                    $record->gambar_hasil_usg_4 ?? null,
                ])
                    ->filter()
                    ->unique()
                    ->values();
            @endphp

            @if ($images->isNotEmpty())
                <div class="usg-images-grid">
                    @foreach ($images as $image)
                        <div class="usg-image-card">
                            <img src="{{ Illuminate\Support\Facades\Storage::url($image) }}" alt="Hasil USG">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="image-container">
                    <div class="empty-image">Gambar hasil USG tidak tersedia.</div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
