<div class="space-y-6">
    {{-- BAGIAN 1: INFORMASI TEKSTUAL (Tidak berubah) --}}
    <div>
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
            Detail Absensi
        </h3>
        <dl class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
            <div class="sm:col-span-2">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Nama Karyawan</dt>
                <dd class="mt-1 text-gray-900 dark:text-white">{{ $data['name'] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Tanggal</dt>
                <dd class="mt-1 text-gray-900 dark:text-white">{{ $data['tanggal_absen'] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Jam Absen</dt>
                <dd class="mt-1 text-gray-900 dark:text-white">{{ $data['jam_absen'] ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    {{-- BAGIAN 2: PETA DAN FOTO DALAM 2 KOLOM --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- KOLOM 1: PETA LEAFLET --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi Absensi</label>

            @if (!empty($data['lokasi_maps']))
                <div x-data="{
                    map: null,
                    coordinates: '{{ $data['lokasi_maps'] }}',
                
                    // FUNGSI BARU: Ini akan menginisialisasi peta SETELAH Leaflet siap
                    initializeMap() {
                        const [lat, lon] = this.coordinates.split(',').map(Number);
                
                        this.map = L.map(this.$refs.mapContainer).setView([lat, lon], 16);
                
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: `© <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors`
                        }).addTo(this.map);
                
                        L.marker([lat, lon]).addTo(this.map)
                            .bindPopup('Lokasi absensi tercatat di sini.')
                            .openPopup();
                
                        setTimeout(() => this.map.invalidateSize(), 50);
                    },
                
                    // FUNGSI UTAMA: Panggil fungsi loader ini saat komponen diinisialisasi
                    init() {
                        // Cek dulu apakah Leaflet sudah ada (misalnya dimuat oleh komponen lain)
                        if (typeof L !== 'undefined') {
                            this.initializeMap();
                            return;
                        }
                
                        // Jika belum ada, kita muat secara dinamis
                        // 1. Muat CSS
                        const cssLink = document.createElement('link');
                        cssLink.rel = 'stylesheet';
                        cssLink.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(cssLink);
                
                        // 2. Muat JavaScript
                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        document.head.appendChild(script);
                
                        // 3. Tunggu sampai skrip selesai dimuat, BARU inisialisasi peta
                        script.onload = () => {
                            this.initializeMap();
                        };
                    }
                }" x-init="init()" wire:ignore>

                    <div x-ref="mapContainer"
                        class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-600 z-0 bg-gray-200">
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Koordinat: {{ $data['lokasi_maps'] }}
                </p>
            @else
                <div class="flex items-center justify-center h-96 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <p class="text-gray-500">Lokasi tidak tersedia.</p>
                </div>
            @endif
        </div>

        {{-- KOLOM 2: FOTO BUKTI --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Foto Bukti</label>
            @if (!empty($data['foto']))
                <a href="{{ asset('storage/' . $data['foto']) }}" target="_blank" class="block">
                    <img src="{{ asset('storage/' . $data['foto']) }}" alt="Foto Absensi"
                        class="rounded-lg w-full h-96 object-cover border border-gray-200 dark:border-gray-700">
                </a>
            @else
                <div class="flex items-center justify-center h-96 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <p class="text-gray-500">Foto tidak tersedia.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- TIDAK DIPERLUKAN LAGI @push('scripts') DI SINI --}}
