<div class="space-y-4">
    <div>
        <h3 class="text-sm font-medium text-gray-500">Nama Karyawan</h3>
        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $data['name'] ?? 'N/A' }}</p>
    </div>
    <div>
        <h3 class="text-sm font-medium text-gray-500">Tanggal & Jam Absen</h3>
        <p class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $data['tanggal_absen'] ?? 'N/A' }} - {{ $data['jam_absen'] ?? 'N/A' }}
        </p>
    </div>
    <div>
        <h3 class="text-sm font-medium text-gray-500">Lokasi</h3>
        <a href="https://www.google.com/maps/search/?api=1&query={{ $data['lokasi_maps'] ?? '' }}" target="_blank"
            class="mt-1 text-sm text-primary-600 hover:underline">
            {{ $data['lokasi_maps'] ?? 'N/A' }}
        </a>
    </div>

    {{-- TAMBAHKAN BLOK KODE INI --}}
    @if (!empty($data['notes']))
        <div>
            <h3 class="text-sm font-medium text-gray-500">Catatan</h3>
            <p class="mt-1 text-sm italic text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 p-2 rounded-md">
                "{{ $data['notes'] }}"
            </p>
        </div>
    @endif
    {{-- AKHIR BLOK KODE --}}

    <div>
        <h3 class="text-sm font-medium text-gray-500">Foto Bukti</h3>
        @if (!empty($data['foto']))
            <img src="{{ asset('storage/' . $data['foto']) }}" alt="Foto Absensi" class="mt-2 rounded-lg max-w-sm">
        @else
            <p class="mt-1 text-sm text-gray-900 dark:text-white">Tidak ada foto.</p>
        @endif
    </div>
</div>
