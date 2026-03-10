<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Audiometri - {{ $record->participant?->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/audiometry-print.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="{{ asset('js/audiometry-print.js') }}" defer></script>
</head>

<body>
    @php
        $formatDb = function ($value): string {
            if ($value === null || $value === '') {
                return '-';
            }

            if (!is_numeric($value)) {
                return (string) $value;
            }

            $formatted = number_format((float) $value, 2, ',', '.');
            $formatted = rtrim(rtrim($formatted, '0'), ',');

            return $formatted;
        };

        $avg = function (array $values): ?float {
            $filtered = array_values(array_filter($values, static fn($v) => is_numeric($v)));
            if (count($filtered) === 0) {
                return null;
            }

            return array_sum($filtered) / count($filtered);
        };

        $frequencies = [250, 500, 1000, 2000, 3000, 4000, 6000, 8000];
        $rightAir = [
            $record->ad_ac_250,
            $record->ad_ac_500,
            $record->ad_ac_1000,
            $record->ad_ac_2000,
            $record->ad_ac_3000,
            $record->ad_ac_4000,
            $record->ad_ac_6000,
            $record->ad_ac_8000,
        ];
        $leftAir = [
            $record->as_ac_250,
            $record->as_ac_500,
            $record->as_ac_1000,
            $record->as_ac_2000,
            $record->as_ac_3000,
            $record->as_ac_4000,
            $record->as_ac_6000,
            $record->as_ac_8000,
        ];
        $rightBone = [
            $record->ad_bc_250,
            $record->ad_bc_500,
            $record->ad_bc_1000,
            $record->ad_bc_2000,
            $record->ad_bc_3000,
            $record->ad_bc_4000,
            $record->ad_bc_6000,
            $record->ad_bc_8000,
        ];
        $leftBone = [
            $record->as_bc_250,
            $record->as_bc_500,
            $record->as_bc_1000,
            $record->as_bc_2000,
            $record->as_bc_3000,
            $record->as_bc_4000,
            $record->as_bc_6000,
            $record->as_bc_8000,
        ];
        $rightBoneAvg = $avg([$record->ad_bc_500, $record->ad_bc_1000, $record->ad_bc_2000, $record->ad_bc_4000]);
        $leftBoneAvg = $avg([$record->as_bc_500, $record->as_bc_1000, $record->as_bc_2000, $record->as_bc_4000]);
    @endphp

    <div id="audiometry-payload" data-right-air='@json($rightAir)' data-left-air='@json($leftAir)' data-right-bone='@json($rightBone)' data-left-bone='@json($leftBone)'></div>

    <div class="container">
        <div class="title">HASIL PEMERIKSAAN AUDIOMETRI</div>

        <table class="info-grid">
            <tr>
                <td class="label-col">No. RM</td>
                <td>:</td>
                <td>{{ $record->no_rm }}</td>
                <td class="label-col right-col">Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $record->participant?->gender ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Nama</td>
                <td>:</td>
                <td>{{ $record->participant?->name }}</td>
                <td class="label-col right-col">Usia</td>
                <td>:</td>
                <td>{{ optional($record->participant?->date_of_birth ? \Carbon\Carbon::parse($record->participant?->date_of_birth) : null)?->age }} Tahun</td>
            </tr>
            <tr>
                <td class="label-col">Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $record->participant?->date_of_birth ? \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d-m-Y') : '-' }}</td>
                <td class="label-col right-col">Pelaksanaan</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="label-col">Instansi</td>
                <td>:</td>
                <td>{{ $record->instansi ?: '-' }}</td>
                <td class="label-col right-col"></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <div class="panel-wrap">
            <div class="ear-panel">
                <div class="ear-title">Dextra</div>
                <div class="graph-box">
                    <canvas id="rightEarChart"></canvas>
                </div>
                <table class="freq-table">
                    <tr>
                        <th class="head head-right" colspan="2">Frequency (Hz)</th>
                        @foreach ($frequencies as $hz)
                            <th class="head head-right">{{ $hz }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="air-right"><strong>Air</strong></td>
                        <td class="air-right"><strong>O</strong></td>
                        @foreach ($rightAir as $value)
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="bone"><strong>Bone</strong></td>
                        <td class="bone"><strong>&lt;</strong></td>
                        @foreach ($rightBone as $value)
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                </table>
            </div>

            <div class="ear-panel">
                <div class="ear-title">Sinistra</div>
                <div class="graph-box">
                    <canvas id="leftEarChart"></canvas>
                </div>
                <table class="freq-table">
                    <tr>
                        <th class="head head-left" colspan="2">Frequency (Hz)</th>
                        @foreach ($frequencies as $hz)
                            <th class="head head-left">{{ $hz }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="air-left"><strong>Air</strong></td>
                        <td class="air-left"><strong>X</strong></td>
                        @foreach ($leftAir as $value)
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="bone"><strong>Bone</strong></td>
                        <td class="bone"><strong>&gt;</strong></td>
                        @foreach ($leftBone as $value)
                            <td>{{ $value ?? '-' }}</td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>

        <div class="summary">
            <div><strong>Derajat Ambang Dengar</strong>:</div>
            <div class="line-row">
                <div class="threshold-col">
                    <div class="line-item line-item-full">
                        <span>Telinga Kanan (AD)</span>
                        <span class="line">{{ $record->derajat_ad ?: '-' }}</span>
                    </div>
                    <div class="line-item line-item-full">
                        <span>Telinga Kiri (AS)</span>
                        <span class="line">{{ $record->derajat_as ?: '-' }}</span>
                    </div>
                </div>
                <div class="threshold-col">
                    <div class="line-item line-item-full">
                        <span>Bone</span>
                        <span class="line">{{ $rightBoneAvg !== null ? $formatDb($rightBoneAvg) . ' dB' : '-' }}</span>
                    </div>
                    <div class="line-item line-item-full">
                        <span>Bone</span>
                        <span class="line">{{ $leftBoneAvg !== null ? $formatDb($leftBoneAvg) . ' dB' : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="summary summary-tight">
            <div class="line-item line-item-full">
                <span class="label-short">Kesimpulan</span>
                <span class="line">{{ $record->kesimpulan ?: '-' }}</span>
            </div>
        </div>

        <div class="summary summary-tight">
            <div class="line-item line-item-full">
                <span class="label-short">Saran</span>
                <span class="line">{{ $record->saran ?: '-' }}</span>
            </div>
        </div>

        <div class="footer-sign">
            <div class="sign-box">
                <div>Dokter Pemeriksa,</div>
                @if ($record->tanda_tangan)
                    <img class="sign-image" src="{{ \App\Support\StoragePublicUrl::fromPath($record->tanda_tangan) }}" alt="TTD Dokter">
                @else
                    <div class="sign-placeholder"></div>
                @endif
                <div class="doctor-name">{{ $record->dokter?->name ?? '-' }}</div>
                <div>{{ $record->dokter?->spesialisasi ?: 'Spesialis THT' }}</div>
            </div>
        </div>
    </div>
</body>

</html>
