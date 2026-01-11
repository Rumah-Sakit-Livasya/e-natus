<x-filament-panels::page>
    {{-- STYLING & PRINT SETUP --}}
    <style>
        /* CSS Variables untuk Theming (Light & Dark Mode) */
        .report-container {
            --bg-color: #ffffff;
            --text-color: #1f2937;
            --text-color-soft: #6b7280;
            --text-color-strong: #000000;
            --border-color: #e5e7eb;
            --border-color-soft: #f3f4f6;
            --section-bg: #dbeafe;
            --section-text: #1e3a8a;
            --section-border: #bfdbfe;
            --prose-link-color: #3b82f6;

            font-family: Arial, sans-serif;
            font-size: 10pt;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .report-container.dark-mode {
            --bg-color: #111827;
            --text-color: #d1d5db;
            --text-color-soft: #9ca3af;
            --text-color-strong: #ffffff;
            --border-color: #4b5563;
            --border-color-soft: #374151;
            --section-bg: #1e3a8a;
            --section-text: #dbeafe;
            --section-border: #3b82f6;
            --prose-link-color: #60a5fa;
        }

        /* Styling Komponen Umum */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .report-header img {
            height: 50px;
        }

        .dark-mode .report-header img {
            background-color: white;
            padding: 2px;
            border-radius: 4px;
        }

        .report-header .hospital-info {
            text-align: right;
            font-size: 9pt;
            color: var(--text-color-soft);
        }

        .report-header .hospital-info strong {
            color: var(--text-color-strong);
        }

        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 2rem;
            color: var(--text-color-strong);
        }

        .section-title {
            background-color: var(--section-bg);
            color: var(--section-text);
            border: 1px solid var(--section-border);
            font-weight: bold;
            text-align: center;
            padding: 8px;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .sub-section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 4px;
            color: var(--text-color-strong);
        }

        .sub-sub-section-title {
            font-weight: bold;
            font-size: 10pt;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: var(--text-color-strong);
        }

        /* Grid untuk menampilkan data key-value */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 2rem;
        }

        .info-item {
            display: grid;
            grid-template-columns: 180px 10px auto;
            align-items: start;
            margin-bottom: 4px;
        }

        .info-item dt {
            color: var(--text-color-soft);
        }

        .info-item dt .rujukan {
            display: block;
            font-size: 8pt;
            font-style: italic;
        }

        .info-item .colon {
            text-align: center;
            color: var(--text-color-soft);
        }

        .info-item dd {
            font-weight: bold;
            color: var(--text-color-strong);
            white-space: pre-wrap;
        }

        /* Layout untuk foto pasien */
        .patient-photo-grid {
            display: grid;
            grid-template-columns: 120px auto;
            gap: 1.5rem;
            align-items: center;
            border: 1px solid var(--border-color);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .patient-photo-grid img {
            width: 110px;
            height: 130px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .patient-photo-grid .placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 110px;
            height: 130px;
            background-color: var(--border-color-soft);
            color: var(--text-color-soft);
        }

        /* Konten dari Rich Editor */
        .prose-content {
            max-width: none;
        }

        .prose-content ul,
        .prose-content ol {
            padding-left: 1.5em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose-content a {
            color: var(--prose-link-color);
            text-decoration: underline;
        }

        .prose-content strong {
            color: var(--text-color-strong);
        }

        /* Blok Tanda Tangan */
        .signature-grid {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            text-align: center;
            width: 45%;
        }

        .signature-space {
            height: 80px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
        }

        /* Lampiran dari Repeater */
        .attachment-list {
            list-style: none;
            padding-left: 0;
            margin-top: 0.5rem;
        }

        .attachment-list li {
            margin-bottom: 0.25rem;
        }

        .attachment-list a {
            color: var(--prose-link-color);
            text-decoration: underline;
        }

        /* Aturan Cetak */
        /*
         * ==================================================================
         *  4. PRINT STYLES
         *  Aturan di dalam blok ini hanya aktif saat mencetak.
         * ==================================================================
         */
        @media print {

            /* Perintah ini menyembunyikan SEMUANYA di dalam <body> */
            body * {
                visibility: hidden;
            }

            /*
             * Kemudian, perintah ini secara spesifik HANYA MENAMPILKAN kembali
             * area dengan kelas .printable-area dan semua elemen di dalamnya.
             * Navbar, sidebar, dan tombol berada di luar area ini, sehingga mereka
             * tetap tersembunyi.
            */
            .printable-area,
            .printable-area * {
                visibility: visible;
            }

            /* Aturan ini memastikan area yang dapat dicetak mengisi seluruh halaman kertas */
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            /* Ini menyembunyikan elemen yang kita tandai secara manual, seperti tombol "Cetak" */
            .no-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
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
    <div id="report-container" class="printable-area report-container p-3">

        {{-- Alokasi variabel untuk kemudahan akses --}}
        @php
            $participant = $record->participant;
            $project = $record->projectRequest;
            $rpgh = $record->riwayat_penyakit_dan_gaya_hidup ?? [];
            $anamnesa = $record->anamnesa ?? [];
            $vitals = $record->hasil_pemeriksaan_vital_sign ?? [];
            $fisik = $record->hasil_pemeriksaan_fisik_dokter ?? [];
            $lab = $record->hasil_laboratorium ?? [];
            $status_kes = $record->status_kesehatan ?? [];
            $penunjang = $record->hasil_pemeriksaan_penunjang ?? [];
            $summary = $record->kesimpulan_dan_saran ?? [];
        @endphp

        {{-- Header Laporan --}}
        <div class="w-100">
            <img src="{{ asset('img/kop-trans.png') }}" @style('width: 100%') alt="Logo IHC">
        </div>

        <div class="report-title mt-3">HASIL PEMERIKSAAN MEDICAL CHECK UP</div>

        {{-- Info Pasien dengan Foto --}}
        <div class="patient-photo-grid">
            @if ($participant->photo)
                <img src="{{ Storage::url($participant->photo) }}" alt="Foto Pasien">
            @else
                <div class="placeholder"><x-heroicon-o-user class="w-16 h-16" /></div>
            @endif
            <div>
                <p style="font-size: 1.25rem; font-weight: bold; color: var(--text-color-strong);">
                    {{ $participant->name ?? '-' }}</p>
                <p>No. Peg: {{ $participant->employee_code ?? '-' }}</p>
                <p>Proyek: {{ $project->name ?? '-' }}</p>
                <p>Tanggal MCU: {{ $record->tanggal_mcu ? $record->tanggal_mcu->format('d F Y') : '-' }}</p>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- 1. ANAMNESA (DARI WIZARD STEP 1 & 2) --}}
        {{-- ====================================================================== --}}
        <div class="section-title">ANAMNESA</div>
        <div class="sub-section-title">A. Identitas Pasien</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>No. MCU</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $record->no_mcu ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Nama</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->name ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>No. Pegawai</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->employee_code ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Tanggal Lahir</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->date_of_birth ? $participant->date_of_birth->format('d F Y') : '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Usia</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->date_of_birth ? $participant->date_of_birth->age . ' Tahun' : '-' }}</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>Jenis Kelamin</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->gender ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Bagian</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->department ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Alamat</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $participant->address ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Tahun MCU</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $record->tanggal_mcu ? $record->tanggal_mcu->format('Y') : '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="sub-section-title">B. Riwayat Penyakit dan Gaya Hidup</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>Riwayat Penyakit Saat Ini</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_penyakit_saat_ini'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Penyakit Dahulu</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_penyakit_dahulu'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Penyakit Keluarga</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_penyakit_keluarga'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Kecelakaan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_kecelakaan'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Vaksinasi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_vaksinasi'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Obsgyn</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_obsgyn'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Riwayat Operasi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['riwayat_operasi'] ?? '-' }}</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>Keluhan Umum</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['keluhan_umum'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Keluhan Dgn Pekerjaan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['keluhan_dgn_pekerjaan'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Konsumsi Buah/Sayur</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['konsumsi_buah_sayur'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Olah Raga</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['olah_raga'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Alergi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['alergi'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Merokok</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['merokok'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Alkohol</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $rpgh['alkohol'] ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="page-break"></div>

        {{-- ====================================================================== --}}
        {{-- 2. PEMERIKSAAN FISIK (DARI WIZARD STEP 3) --}}
        {{-- ====================================================================== --}}
        <div class="section-title">PEMERIKSAAN FISIK</div>
        <div class="sub-section-title">A. Hazard</div>
        <dl class="info-item">
            <dt>Hazard</dt>
            <dd class="colon">:</dd>
            <dd>{{ $anamnesa['hazard'] ?? '-' }}</dd>
        </dl>

        <div class="sub-section-title">B. Hasil Pemeriksaan Vital Sign</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>Berat Badan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['berat_badan'] ?? '-' }} Kg</dd>
                </dl>
                <dl class="info-item">
                    <dt>Tinggi Badan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['tinggi_badan'] ?? '-' }} cm</dd>
                </dl>
                <dl class="info-item">
                    <dt>Ratio IMT / BMI</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['ratio_imt_bmi'] ?? '-' }} Kg/m²</dd>
                </dl>
                <dl class="info-item">
                    <dt>Status Gizi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['status_gizi'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Lingkar Perut</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['lingkar_perut'] ?? '-' }} cm</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>TD Sistole/Diastole</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['td_sistole'] ?? '-' }}/{{ $vitals['td_diastole'] ?? '-' }} mm Hg</dd>
                </dl>
                <dl class="info-item">
                    <dt>Nadi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['nadi'] ?? '-' }} /menit</dd>
                </dl>
                <dl class="info-item">
                    <dt>Suhu Badan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['suhu_badan'] ?? '-' }} °C</dd>
                </dl>
                <dl class="info-item">
                    <dt>Pernafasan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $vitals['pernafasan'] ?? '-' }} /menit</dd>
                </dl>
            </div>
        </div>

        <div class="sub-section-title">C. Hasil Pemeriksaan Fisik Dokter MCU</div>
        <div class="info-grid">
            <!-- Kolom Kiri -->
            <div>
                <dl class="info-item">
                    <dt>Kepala</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['kepala'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Leher</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['leher'] ?? '-' }}</dd>
                </dl>

                <div class="sub-sub-section-title">Mata</div>
                <dl class="info-item">
                    <dt>AVOD</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['mata']['avod'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>AVOS</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['mata']['avos'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Buta Warna</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['buta_warna'] ?? '-' }}</dd>
                </dl>

                <div class="sub-sub-section-title">THT</div>
                <dl class="info-item">
                    <dt>Telinga</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['tht']['telinga'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Hidung</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['tht']['hidung'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Tenggorokan</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['tht']['tenggorokan'] ?? '-' }}</dd>
                </dl>

                <div class="sub-sub-section-title">Gigi dan Mulut</div>
                <dl class="info-item">
                    <dt>DMF</dt>
                    <dd class="colon">:</dd>
                    <dd>D:{{ $fisik['gigi']['d'] ?? '0' }} M:{{ $fisik['gigi']['m'] ?? '0' }}
                        F:{{ $fisik['gigi']['f'] ?? '0' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Advice Gigi</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['gigi']['advice'] ?? '-' }}</dd>
                </dl>
            </div>
            <!-- Kolom Kanan -->
            <div>
                <dl class="info-item">
                    <dt>Kesadaran</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['kesadaran'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Thorax (Jantung & Paru)</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['thorax'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Abdomen</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['abdomen'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Extermitas</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['extermitas'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Kulit</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['kulit'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Lain - Lain</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['lain_lain'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Dokter Pemeriksa Fisik</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $fisik['dokter_pemeriksa'] ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        <div class="page-break"></div>

        {{-- ====================================================================== --}}
        {{-- 3. HASIL LABORATORIUM (DARI WIZARD STEP 4) --}}
        {{-- ====================================================================== --}}
        <div class="section-title">HASIL PEMERIKSAAN LABORATORIUM</div>
        <div class="sub-section-title">A. Darah Lengkap</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>Hemoglobin<span class="rujukan">R: 12.0-18.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['hemoglobin'] ?? '-' }} g/dl</dd>
                </dl>
                <dl class="info-item">
                    <dt>Hematokrit<span class="rujukan">R: 36.0-54.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['hematokrit'] ?? '-' }} vol.%</dd>
                </dl>
                <dl class="info-item">
                    <dt>Lekosit<span class="rujukan">R: 5.00-10.00</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['lekosit'] ?? '-' }} ribu/uL</dd>
                </dl>
                <dl class="info-item">
                    <dt>Trombosit<span class="rujukan">R: 150-450</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['trombosit'] ?? '-' }} ribu/uL</dd>
                </dl>
                <dl class="info-item">
                    <dt>LED<span class="rujukan">R: 0-10</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['led_i'] ?? '-' }} mm/jam</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>Basofil<span class="rujukan">R: 0.0-1.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['basofil'] ?? '-' }} %</dd>
                </dl>
                <dl class="info-item">
                    <dt>Eosinofil<span class="rujukan">R: 1.0-3.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['eosinofil'] ?? '-' }} %</dd>
                </dl>
                <dl class="info-item">
                    <dt>Segmen<span class="rujukan">R: 40.0-60.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['segmen'] ?? '-' }} %</dd>
                </dl>
                <dl class="info-item">
                    <dt>Limposit<span class="rujukan">R: 20.0-45.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['limposit'] ?? '-' }} %</dd>
                </dl>
                <dl class="info-item">
                    <dt>Monosit<span class="rujukan">R: 0.0-1.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['darah_lengkap']['monosit'] ?? '-' }} %</dd>
                </dl>
            </div>
        </div>

        <div class="sub-section-title">B. Faal Hati, Lemak dan Ginjal</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>SGOT<span class="rujukan">R: <= 41</span>
                    </dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['faal_hati']['sgot'] ?? '-' }} u/l</dd>
                </dl>
                <dl class="info-item">
                    <dt>SGPT<span class="rujukan">R: <= 40</span>
                    </dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['faal_hati']['sgpt'] ?? '-' }} u/l</dd>
                </dl>
                <dl class="info-item">
                    <dt>Glukosa Puasa<span class="rujukan">R: 70.0-105.0</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['faal_hati']['glukosa_puasa'] ?? '-' }} mg/dl</dd>
                </dl>
                <dl class="info-item">
                    <dt>HbA1C<span class="rujukan">R: 4.6-6.6</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['lemak_ginjal']['hba1c'] ?? '-' }} %</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>Cholest Total<span class="rujukan">R: 0-200</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['lemak_ginjal']['cholest_total'] ?? '-' }} mg/dl</dd>
                </dl>
                <dl class="info-item">
                    <dt>Triglyceride<span class="rujukan">R: 0-150</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['lemak_ginjal']['triglyceride'] ?? '-' }} mg/dl</dd>
                </dl>
                <dl class="info-item">
                    <dt>LDL Cholest<span class="rujukan">R: 0-100</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['lemak_ginjal']['ldl_cholest'] ?? '-' }} mg/dl</dd>
                </dl>
                <dl class="info-item">
                    <dt>Asam Urat<span class="rujukan">R: 3.40-7.00</span></dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['lemak_ginjal']['asam_urat'] ?? '-' }} mg/dl</dd>
                </dl>
            </div>
        </div>

        <div class="sub-section-title">C. Immunoserologi & Urin Lengkap</div>
        <div class="info-grid">
            <div>
                <dl class="info-item">
                    <dt>HbsAg</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['immunoserologi']['hbsag'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Anti HBs</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['immunoserologi']['anti_hbs'] ?? '-' }}</dd>
                </dl>
            </div>
            <div>
                <dl class="info-item">
                    <dt>Warna Urin</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['urin']['warna_urin'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Protein Urin</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $lab['urin']['protein_urin'] ?? '-' }}</dd>
                </dl>
            </div>
        </div>
        <p style="margin-top: 1rem; font-size: 9pt; color: var(--text-color-soft);">Penanggung Jawab Lab:
            {{ $lab['penanggung_jawab'] ?? '-' }}</p>

        <div class="page-break"></div>

        {{-- ====================================================================== --}}
        {{-- 4. HASIL PEMERIKSAAN PENUNJANG (DARI WIZARD STEP 5 & 6) --}}
        {{-- ====================================================================== --}}
        <div class="section-title">HASIL PEMERIKSAAN PENUNJANG</div>
        <div class="info-grid">
            <div>
                <div class="sub-sub-section-title">Radiologi</div>
                <dl class="info-item">
                    <dt>Foto Thorax</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['radiologi']['foto_thorax'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>USG Abdomen</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['radiologi']['usg_abdomen'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Dokter PJ</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['radiologi']['dokter_penanggung_jawab'] ?? '-' }}</dd>
                </dl>

                <div class="sub-sub-section-title">EKG & Treadmill</div>
                <dl class="info-item">
                    <dt>EKG Resting</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['ekg']['ekg_resting'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Treadmill Test</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['ekg']['treadmill_test'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Dokter PJ</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['ekg']['dokter_penanggung_jawab'] ?? '-' }}</dd>
                </dl>
            </div>
            <div>
                <div class="sub-sub-section-title">Audiometri</div>
                <dl class="info-item">
                    <dt>Hasil</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['audiometri']['hasil'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Dokter PJ</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['audiometri']['dokter_penanggung_jawab'] ?? '-' }}</dd>
                </dl>

                <div class="sub-sub-section-title">Spirometri</div>
                <dl class="info-item">
                    <dt>Hasil</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['spirometri']['hasil'] ?? '-' }}</dd>
                </dl>
                <dl class="info-item">
                    <dt>Dokter PJ</dt>
                    <dd class="colon">:</dd>
                    <dd>{{ $penunjang['spirometri']['dokter_penanggung_jawab'] ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Menampilkan data dari Repeater --}}
        @if (!empty($penunjang['pemeriksaan_dinamis']) && is_array($penunjang['pemeriksaan_dinamis']))
            @foreach ($penunjang['pemeriksaan_dinamis'] as $item)
                @if (!empty($item['judul']))
                    <div class="sub-section-title">{{ $item['judul'] }}</div>
                    <div class="prose-content">{!! $item['isi'] ?? 'Tidak ada detail.' !!}</div>

                    @if (!empty($item['lampiran']) && is_array($item['lampiran']))
                        <div class="sub-sub-section-title" style="margin-top: 0.5rem;">Lampiran:</div>
                        <ul class="attachment-list">
                            @foreach ($item['lampiran'] as $filePath)
                                <li><a href="{{ Storage::url($filePath) }}"
                                        target="_blank">{{ basename($filePath) }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                @endif
            @endforeach
        @endif

        <div class="page-break"></div>

        {{-- ====================================================================== --}}
        {{-- 5. KESIMPULAN, CATATAN & SARAN (DARI WIZARD STEP 7) --}}
        {{-- ====================================================================== --}}
        <div class="section-title">KESIMPULAN, CATATAN & SARAN</div>

        <div class="sub-section-title">Kesimpulan</div>
        <div class="prose-content">{!! $summary['kesimpulan'] ?? '-' !!}</div>

        <div class="sub-section-title">Catatan</div>
        <div class="prose-content">{!! $summary['catatan'] ?? '-' !!}</div>

        <div class="sub-section-title">Saran</div>
        <div class="prose-content">{!! $summary['saran'] ?? '-' !!}</div>

        <div class="sub-section-title">Nasehat</div>
        <div class="prose-content">{!! $summary['nasehat'] ?? '-' !!}</div>

        <div class="sub-section-title">Diet</div>
        <div class="prose-content">{!! $summary['diet'] ?? '-' !!}</div>

        <div class="signature-grid">
            <div class="signature-block">
                <p>Dokter Pemeriksa,</p>
                <div class="signature-space"></div>
                <p><strong>{{ $summary['dokter_pemeriksa_nama'] ?? '-' }}</strong></p>
            </div>
            <div class="signature-block">
                <p>Penanggung Jawab MCU dan OH- IH,</p>
                <div class="signature-space"></div>
                <p><strong>{{ $summary['penanggung_jawab_nama'] ?? '-' }}</strong></p>
                <p style="font-size: 9pt;">{{ $summary['penanggung_jawab_sip'] ?? '-' }}</p>
                <p style="font-size: 9pt;">{{ $summary['penanggung_jawab_oguk'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{--
      * ==================================================================
      *  5. JAVASCRIPT "GLUE"
      *  Skrip ini menghubungkan tema Filament dengan CSS kustom kita.
      *  Diawasi agar tidak ikut tercetak (no-print).
      * ==================================================================
    --}}
    <script class="no-print">
        document.addEventListener('DOMContentLoaded', () => {
            const reportContainer = document.getElementById('report-container');
            const htmlElement = document.documentElement;

            // Fungsi untuk menerapkan kelas .dark-mode berdasarkan kelas di <html>
            const syncDarkMode = () => {
                if (htmlElement.classList.contains('dark')) {
                    reportContainer.classList.add('dark-mode');
                } else {
                    reportContainer.classList.remove('dark-mode');
                }
            };

            // Jalankan sekali saat halaman dimuat
            syncDarkMode();

            // Gunakan MutationObserver untuk mendeteksi perubahan tema secara real-time
            // (saat pengguna mengklik tombol toggle tema Filament)
            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        syncDarkMode();
                    }
                }
            });

            // Mulai mengamati perubahan pada atribut 'class' dari elemen <html>
            observer.observe(htmlElement, {
                attributes: true
            });
        });
    </script>

</x-filament-panels::page>
