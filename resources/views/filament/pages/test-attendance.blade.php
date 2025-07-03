<x-filament-panels::page>
    <div class="p-4 bg-white rounded-lg shadow">
        <h1 class="text-xl font-bold">Tes Halaman Absensi</h1>
        <p class="mt-2">Jika Anda melihat ini, berarti halaman berhasil dimuat.</p>
        <div class="mt-4 p-2 bg-gray-100 border rounded">
            <p><strong>ID Proyek:</strong> {{ $record->id }}</p>
            <p><strong>Nama Proyek:</strong> {{ $record->name }}</p>
        </div>
    </div>
</x-filament-panels::page>
