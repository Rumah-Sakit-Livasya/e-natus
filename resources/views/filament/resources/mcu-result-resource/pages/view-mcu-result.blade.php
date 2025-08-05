<x-filament-panels::page>

    {{-- STYLING & PRINT SETUP --}}
    <style>
        .mcu-container {
            font-family: sans-serif;
            color: #333;
        }

        .mcu-section {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: white;
        }

        .mcu-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            border-bottom: 2px solid #666;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            color: #111;
        }

        .mcu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .mcu-item {
            display: grid;
            grid-template-columns: 140px auto;
        }

        .mcu-item dt {
            font-weight: 500;
            color: #4b5563;
        }

        .mcu-item dd {
            font-weight: 600;
        }

        .mcu-table {
            width: 100%;
            margin-top: 1rem;
            border-collapse: collapse;
        }

        .mcu-table th,
        .mcu-table td {
            border: 1px solid #d1d5db;
            padding: 0.75rem;
            text-align: left;
        }

        .mcu-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .attachment-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .attachment-item {
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .attachment-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        .attachment-item figcaption {
            padding: 0.75rem;
            background-color: #f9fafb;
            text-align: center;
            font-weight: 500;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .printable-area,
            .printable-area * {
                visibility: visible;
            }

            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>

    {{-- ACTION BUTTON --}}
    <div class="flex justify-end mb-4 no-print">
        <x-filament::button icon="heroicon-o-printer" onclick="window.print()">
            Cetak Laporan
        </x-filament::button>
    </div>

    {{-- REPORT CONTENT --}}
    <div class="printable-area mcu-container">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold uppercase">Hasil Pemeriksaan Medical Check Up</h1>
            <p class="text-lg">{{ $record->projectRequest->client->name ?? 'Rumah Sakit Pertamina Cilacap' }}</p>
        </div>

        <div class="mcu-section">
            <h2>Identitas Pasien</h2>
            <div class="mcu-grid">
                <dl class="mcu-item">
                    <dt>No. MCU</dt>
                    <dd>: {{ $record->no_mcu }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Tanggal MCU</dt>
                    <dd>: {{ $record->tanggal_mcu->format('d F Y') }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Nama</dt>
                    <dd>: {{ $record->participant->name }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>No. Pegawai</dt>
                    <dd>: {{ $record->participant->employee_code }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Tgl. Lahir / Usia</dt>
                    <dd>: {{ $record->participant->date_of_birth->format('d F Y') }}
                        ({{ $record->participant->date_of_birth->age }} Tahun)</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Jenis Kelamin</dt>
                    <dd>: {{ $record->participant->gender }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Departemen</dt>
                    <dd>: {{ $record->participant->department }}</dd>
                </dl>
                <dl class="mcu-item">
                    <dt>Status</dt>
                    <dd>: {{ $record->participant->marital_status }}</dd>
                </dl>
            </div>
        </div>

        @if (!empty($record->hasil_pemeriksaan_fisik_dokter))
            <div class="mcu-section">
                <h2>Pemeriksaan Fisik</h2>
                <table class="mcu-table">
                    <thead>
                        <tr>
                            <th>Area Tubuh</th>
                            <th>Hasil</th>
                            <th>Kesimpulan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->hasil_pemeriksaan_fisik_dokter as $item)
                            <tr>
                                <td>{{ $item['area'] }}</td>
                                <td>{{ $item['hasil'] }}</td>
                                <td>{{ $item['kesimpulan'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if (!empty($record->attachments))
            <div class="mcu-section">
                <h2>Lampiran Gambar & Dokumen</h2>
                <div class="attachment-gallery">
                    @foreach ($record->attachments as $attachment)
                        <figure class="attachment-item">
                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank">
                                <img src="{{ Storage::url($attachment->file_path) }}"
                                    alt="{{ $attachment->description }}">
                            </a>
                            <figcaption>{{ $attachment->description }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!empty($record->kesimpulan_dan_saran))
            <div class="mcu-section">
                <h2>Kesimpulan & Saran</h2>
                <div>
                    <h3 class="font-bold text-lg mt-4">Kesimpulan</h3>
                    <div class="prose max-w-none">{!! $record->kesimpulan_dan_saran['kesimpulan'] ?? '-' !!}</div>

                    <h3 class="font-bold text-lg mt-4">Catatan</h3>
                    <div class="prose max-w-none">{!! $record->kesimpulan_dan_saran['catatan'] ?? '-' !!}</div>

                    <h3 class="font-bold text-lg mt-4">Saran</h3>
                    <div class="prose max-w-none">{!! $record->kesimpulan_dan_saran['saran'] ?? '-' !!}</div>
                </div>
            </div>
        @endif
    </div>

</x-filament-panels::page>
