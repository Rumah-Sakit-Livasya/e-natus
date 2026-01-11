<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Audiometri - {{ $record->participant?->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap');

        body {
            font-family: 'Roboto Mono', monospace;
            margin: 0;
            padding: 20px;
            background-color: #fdfdfd;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 25px;
        }

        h1 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 30px;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 8px;
            font-size: 14px;
        }

        .label {
            width: 15%;
        }

        .separator {
            width: 2%;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 10px;
        }

        .chart-wrapper {
            width: 48%;
            border: 1px solid #999;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 13px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #999;
            padding: 5px;
        }

        .data-table th {
            background-color: #eee;
        }

        .summary-section {
            margin-top: 20px;
            font-size: 14px;
        }

        .summary-section table {
            width: 100%;
        }

        .summary-section td {
            padding: 4px 8px;
        }

        .kesimpulan-text {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 50px;
        }

        @media print {
            body {
                background-color: #fff;
            }

            .container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>HASIL PEMERIKSAAN AUDIOMETRI</h1>

        <table class="info-table">
            <tr>
                <td class="label">No. RM</td>
                <td class="separator">:</td>
                <td>{{ $record->no_rm }}</td>
                <td class="label">Jenis Kelamin</td>
                <td class="separator">:</td>
                <td>{{ $record->participant?->gender }}</td>
            </tr>
            <tr>
                <td class="label">Nama</td>
                <td class="separator">:</td>
                <td>{{ $record->participant?->name }}</td>
                <td class="label">Instansi</td>
                <td class="separator">:</td>
                <td>{{ $record->instansi }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Lahir</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->format('d-m-Y') }}</td>
                <td class="label">Pelaksanaan</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="label">Usia</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($record->participant?->date_of_birth)->age }} Tahun</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <div class="chart-container">
            <div class="chart-wrapper">
                <canvas id="rightEarChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <canvas id="leftEarChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div style="width: 48%;">
                <table class="data-table">
                    <tr>
                        <th>Frequency (Hz)</th>
                        <th>250</th>
                        <th>500</th>
                        <th>1000</th>
                        <th>2000</th>
                        <th>3000</th>
                        <th>4000</th>
                        <th>6000</th>
                        <th>8000</th>
                    </tr>
                    <tr>
                        <td style="background-color: #ffdddd;"><strong>Air</strong></td>
                        <td>{{ $record->ad_ac_250 }}</td>
                        <td>{{ $record->ad_ac_500 }}</td>
                        <td>{{ $record->ad_ac_1000 }}</td>
                        <td>{{ $record->ad_ac_2000 }}</td>
                        <td>{{ $record->ad_ac_3000 }}</td>
                        <td>{{ $record->ad_ac_4000 }}</td>
                        <td>{{ $record->ad_ac_6000 }}</td>
                        <td>{{ $record->ad_ac_8000 }}</td>
                    </tr>
                </table>
            </div>
            <div style="width: 48%;">
                <table class="data-table">
                    <tr>
                        <th>Frequency (Hz)</th>
                        <th>250</th>
                        <th>500</th>
                        <th>1000</th>
                        <th>2000</th>
                        <th>3000</th>
                        <th>4000</th>
                        <th>6000</th>
                        <th>8000</th>
                    </tr>
                    <tr>
                        <td style="background-color: #ddeeff;"><strong>Air</strong></td>
                        <td>{{ $record->as_ac_250 }}</td>
                        <td>{{ $record->as_ac_500 }}</td>
                        <td>{{ $record->as_ac_1000 }}</td>
                        <td>{{ $record->as_ac_2000 }}</td>
                        <td>{{ $record->as_ac_3000 }}</td>
                        <td>{{ $record->as_ac_4000 }}</td>
                        <td>{{ $record->as_ac_6000 }}</td>
                        <td>{{ $record->as_ac_8000 }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="summary-section">
            <table>
                <tr>
                    <td style="width: 25%; color: #cc0000; font-style: italic;">Derajat Ambang Dengar:</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Telinga Kanan (AD):</td>
                    <td><strong>{{ $record->derajat_ad }}</strong></td>
                </tr>
                <tr>
                    <td>Telinga Kiri (AS):</td>
                    <td><strong>{{ $record->derajat_as }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="summary-section">
            <table>
                <tr>
                    <td style="width: 25%; color: #cc0000; font-style: italic;">Kesimpulan:</td>
                    <td class="kesimpulan-text">{{ $record->kesimpulan }}</td>
                </tr>
            </table>
        </div>

        <div class="summary-section">
            <table>
                <tr>
                    <td style="width: 25%;">Saran:</td>
                    <td>{{ $record->saran }}</td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        const frequencies = ['250', '500', '1000', '2000', '3000', '4000', '6000', '8000'];

        const rightEarData = [
            {{ $record->ad_ac_250 ?? 'null' }}, {{ $record->ad_ac_500 ?? 'null' }},
            {{ $record->ad_ac_1000 ?? 'null' }},
            {{ $record->ad_ac_2000 ?? 'null' }}, {{ $record->ad_ac_3000 ?? 'null' }},
            {{ $record->ad_ac_4000 ?? 'null' }},
            {{ $record->ad_ac_6000 ?? 'null' }}, {{ $record->ad_ac_8000 ?? 'null' }}
        ];

        const leftEarData = [
            {{ $record->as_ac_250 ?? 'null' }}, {{ $record->as_ac_500 ?? 'null' }},
            {{ $record->as_ac_1000 ?? 'null' }},
            {{ $record->as_ac_2000 ?? 'null' }}, {{ $record->as_ac_3000 ?? 'null' }},
            {{ $record->as_ac_4000 ?? 'null' }},
            {{ $record->as_ac_6000 ?? 'null' }}, {{ $record->as_ac_8000 ?? 'null' }}
        ];

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y} dB`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Frequency (Hz)'
                    },
                    grid: {
                        color: '#ccc'
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: 'Hearing level in Decibels (dB)'
                    },
                    reverse: true,
                    min: -10,
                    max: 120,
                    ticks: {
                        stepSize: 10
                    },
                    grid: {
                        color: '#ccc'
                    }
                }
            }
        };

        // Chart Telinga Kanan
        new Chart(document.getElementById('rightEarChart'), {
            type: 'line',
            data: {
                labels: frequencies,
                datasets: [{
                    label: 'Telinga Kanan (AD)',
                    data: rightEarData,
                    borderColor: 'red',
                    backgroundColor: 'red',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    spanGaps: true // <-- Menyambungkan garis meski ada data null
                }]
            },
            options: chartOptions
        });

        // Chart Telinga Kiri
        new Chart(document.getElementById('leftEarChart'), {
            type: 'line',
            data: {
                labels: frequencies,
                datasets: [{
                    label: 'Telinga Kiri (AS)',
                    data: leftEarData,
                    borderColor: 'blue',
                    backgroundColor: 'blue',
                    pointStyle: 'crossRot', // <-- Membuat titik menjadi 'x'
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    spanGaps: true
                }]
            },
            options: chartOptions
        });
    </script>

    <script>
        // Jeda 500 milidetik (0.5 detik) untuk memastikan semua elemen,
        // terutama grafik, sudah selesai digambar sebelum dialog print muncul.
        setTimeout(function() {
            window.print();
        }, 500);
    </script>

</body>

</html>
