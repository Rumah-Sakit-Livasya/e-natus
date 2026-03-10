<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil USG Mammae - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/medical-check-print.css') }}">
    <link rel="stylesheet" href="{{ asset('css/usg-mammae-print.css') }}">
    <script src="{{ asset('js/medical-check-print.js') }}" defer></script>
</head>

<body class="print-page">
    @php
        $toBullets = function (?string $text): array {
            $lines = preg_split('/\r\n|\r|\n/', (string) $text) ?: [];
            return collect($lines)
                ->map(fn(string $line) => trim($line))
                ->filter()
                ->map(fn(string $line) => ltrim($line, "-• \t"))
                ->values()
                ->all();
        };

        $kananBullets = $toBullets($record->mammae_kanan);
        $kiriBullets = $toBullets($record->mammae_kiri);

        $images = collect($record->gambar_hasil_usg_lampiran ?? [])
            ->merge([
            $record->gambar_hasil_usg,
            $record->gambar_hasil_usg_2,
            $record->gambar_hasil_usg_3,
            $record->gambar_hasil_usg_4,
            $record->gambar_hasil_usg_5,
            $record->gambar_hasil_usg_6,
            ])
            ->filter()
            ->unique()
            ->values();
    @endphp

    <div class="container">
        <div class="report-title">HASIL PEMERIKSAAN USG MAMMAE</div>
        <div class="report-subtitle">( RADIOLOGI )</div>

        @include('pemeriksaan.partials.usg-mammae-patient-header', ['showPekerja' => false])

        <div class="usg-mammae-body">
            <div class="bold">Telah dilakukan pemeriksaan USG Mammae bilateral, dengan hasil:</div>

            <div class="finding-group">
                <div class="finding-title">Mammae kanan:</div>
                <ul class="finding-list">
                    @foreach ($kananBullets as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="finding-group">
                <div class="finding-title">Mammae kiri:</div>
                <ul class="finding-list">
                    @foreach ($kiriBullets as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-10">{{ $record->catatan_tambahan }}</div>

            <div class="mt-10">
                <div class="bold">Kesimpulan:</div>
                <div class="preserve-line">{{ $record->kesimpulan }}</div>
            </div>

            <div class="signature-block">
                <div class="center">Radiologist</div>
                <div class="signature-area center">
                    @if ($record->tanda_tangan)
                        <img src="{{ \App\Support\StoragePublicUrl::fromPath($record->tanda_tangan) }}" class="ttd-image" alt="TTD">
                    @endif
                </div>
                <div class="doctor-name">{{ $record->radiologist }}</div>
            </div>
        </div>
    </div>

    @if ($images->isNotEmpty())
        <div class="page-break print-page">
            <div class="container">
                @include('pemeriksaan.partials.usg-mammae-patient-header', ['showPekerja' => true])

                <div class="usg-mammae-grid">
                    @foreach ($images as $image)
                        <div class="usg-mammae-card">
                            <img src="{{ Illuminate\Support\Facades\Storage::url($image) }}" alt="Hasil USG Mammae">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</body>

</html>
