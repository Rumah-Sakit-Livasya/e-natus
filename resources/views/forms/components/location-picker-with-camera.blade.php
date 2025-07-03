{{--
    Komponen Blade ini menggabungkan Peta Leaflet dan Kamera menjadi satu unit.
    - Dikontrol oleh Alpine.js.
    - Mengisi dua field tersembunyi (HiddenInput) yang didefinisikan di Filament Resource:
      1. 'data.lokasi_maps' (diisi oleh fungsi getLocation)
      2. 'data.foto' (diisi oleh fungsi takePhoto)
--}}
<div x-data="locationPickerWithCamera({
    lokasiStatePath: 'data.lokasi_maps',
    fotoStatePath: 'data.foto'
})">

    {{-- Kontainer utama dengan Grid System yang responsif --}}
    {{-- Akan menjadi 1 kolom di layar kecil, dan 2 kolom di layar medium ke atas (md:) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- KOLOM 1: PETA LEAFLET --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                1. Lokasi (Terdeteksi Otomatis)
            </label>

            {{-- Kontainer peta. Kita hapus h-64 karena @style lebih spesifik. --}}
            <div wire:ignore id="map" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 z-0"
                @style('height: 30rem;')>
                <div class="h-full w-full flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-500">
                    <x-filament::loading-indicator class="h-6 w-6 mr-2" />
                    Memuat peta dan mencari lokasi...
                </div>
            </div>

            {{-- Tampilan teks alamat/koordinat --}}
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <strong>Alamat Terdeteksi:</strong>
                <span x-text="locationText">Mencari lokasi, mohon tunggu...</span>
            </div>
        </div>

        {{-- KOLOM 2: KAMERA --}}
        {{-- Kita gunakan flexbox di sini agar tinggi kamera bisa menyesuaikan tinggi peta --}}
        <div class="flex flex-col space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">2. Ambil Foto Bukti</label>

            {{-- Wrapper untuk kamera dan tombol, agar tombol tetap di bawah --}}
            <div class="flex flex-col items-center flex-grow">
                {{-- Kontainer kamera dibuat flex-grow agar mengisi ruang yang tersedia --}}
                <div
                    class="w-full bg-gray-900 text-white flex items-center justify-center rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 flex-grow">
                    <div x-show="isLoading" class="flex flex-col items-center">
                        <x-filament::loading-indicator class="h-8 w-8" />
                        <p class="mt-2 text-sm">Menyalakan kamera...</p>
                    </div>
                    <video x-ref="video" x-show="!isLoading && !photoTaken" @canplay="isReady = true"
                        class="w-full h-full object-cover" autoplay playsinline muted></video>
                    <canvas x-ref="canvas" class="hidden"></canvas>
                    <img x-show="photoTaken" :src="photoPreviewUrl" class="w-full h-full object-cover"
                        alt="Preview Foto Bukti">
                </div>

                {{-- Tombol-tombol --}}
                <div class="flex gap-x-3 mt-4">
                    <x-filament::button type="button" @click="takePhoto()" x-show="!photoTaken" x-disabled="!isReady"
                        icon="heroicon-m-camera">
                        <span x-show="!isReady">Tunggu Kamera</span>
                        <span x-show="isReady">Ambil Foto</span>
                    </x-filament::button>
                    <x-filament::button type="button" color="gray" @click="retakePhoto()" x-show="photoTaken"
                        icon="heroicon-m-arrow-path">
                        Ulangi Foto
                    </x-filament::button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Semua skrip yang dibutuhkan oleh komponen ini, dimuat di akhir halaman (TIDAK ADA PERUBAHAN) --}}
@push('scripts')
    {{-- Memuat library Leaflet (CSS dan JS) dari CDN --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    {{-- Logika utama menggunakan Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('locationPickerWithCamera', (config) => ({
                // State Kamera
                isLoading: true,
                isReady: false,
                photoTaken: false,
                photoPreviewUrl: null,
                stream: null,

                // State Lokasi/Peta
                locationText: 'Mencari lokasi, mohon tunggu...',
                map: null,
                marker: null,

                init() {
                    this.$el.addEventListener('livewire:navigating', () => this.stopCamera());
                    setTimeout(() => {
                        this.startCamera();
                        this.initMap();
                        this.getLocation();
                    }, 200);

                    // Menyesuaikan ukuran peta saat ukuran window berubah,
                    // karena peta mungkin tidak merender ulang dengan benar setelah perubahan grid
                    window.addEventListener('resize', () => {
                        if (this.map) {
                            setTimeout(() => {
                                this.map.invalidateSize();
                            }, 100);
                        }
                    });
                },

                // === FUNGSI KAMERA (TIDAK DIUBAH) ===
                startCamera() {
                    this.isLoading = true;
                    this.isReady = false;
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Kamera tidak didukung oleh browser ini.');
                        this.isLoading = false;
                        return;
                    }
                    navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user'
                            },
                            audio: false
                        })
                        .then(stream => {
                            this.stream = stream;
                            this.$refs.video.srcObject = stream;
                            this.isLoading = false;
                        }).catch(err => {
                            console.error("Error starting camera:", err);
                            this.isLoading = false;
                            new Notification().title('Gagal Kamera').body(
                                'Tidak bisa mengakses kamera. Pastikan Anda memberikan izin.'
                            ).danger().send();
                        });
                },
                takePhoto() {
                    if (!this.isReady) return;
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video
                        .videoHeight);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                    this.photoPreviewUrl = dataUrl;
                    this.$wire.set(config.fotoStatePath, dataUrl);
                    this.photoTaken = true;
                    this.stopCamera();
                },
                retakePhoto() {
                    this.photoPreviewUrl = null;
                    this.$wire.set(config.fotoStatePath, null);
                    this.photoTaken = false;
                    this.$nextTick(() => this.startCamera());
                },
                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                    this.stream = null;
                    this.isReady = false;
                },

                // === FUNGSI PETA (TIDAK DIUBAH) ===
                initMap() {
                    if (typeof L === 'undefined') {
                        console.error('Leaflet is not loaded!');
                        document.getElementById('map').innerHTML =
                            '<div class="h-full w-full flex items-center justify-center bg-red-100 text-red-700 p-4 text-center">Gagal memuat pustaka peta. Periksa koneksi internet Anda.</div>';
                        return;
                    }
                    this.map = L.map('map').setView([-2.5489, 118.0149], 5);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(this.map);
                    this.marker = L.marker([-2.5489, 118.0149]).addTo(this.map)
                        .bindPopup("Mencari lokasi Anda...");
                },
                getLocation() {
                    if (!navigator.geolocation) {
                        this.locationText = 'Geolocation tidak didukung oleh browser ini.';
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            const latLngString = `${lat},${lon}`;
                            this.$wire.set(config.lokasiStatePath, latLngString);
                            this.map.setView([lat, lon], 16);
                            this.marker.setLatLng([lat, lon]);
                            this.reverseGeocode(lat, lon);
                        },
                        (error) => {
                            this.locationText = `Gagal mendapatkan lokasi: ${error.message}`;
                            console.error("Geolocation error:", error);
                            this.marker.getPopup().setContent(
                                `<b>Gagal mendapatkan lokasi:</b><br>${error.message}`
                            ).openPopup();
                            new Notification().title('Gagal Lokasi').body(error.message)
                                .danger().send();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );
                },
                async reverseGeocode(lat, lon) {
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`
                        );
                        if (!response.ok) throw new Error('Network response was not ok.');
                        const data = await response.json();
                        const address = data?.display_name ||
                            `Koordinat: ${lat.toFixed(5)}, ${lon.toFixed(5)}`;
                        this.locationText = address;
                        this.marker.getPopup().setContent(`<b>Lokasi Anda:</b><br>${address}`)
                            .openPopup();
                    } catch (error) {
                        console.error('Reverse geocoding error:', error);
                        const coordsText =
                            `Koordinat: ${lat.toFixed(5)}, ${lon.toFixed(5)}`;
                        this.locationText = coordsText;
                        this.marker.getPopup().setContent(
                            `<b>Lokasi Terdeteksi</b><br>${coordsText}`).openPopup();
                    }
                }
            }));
        });
    </script>
@endpush
