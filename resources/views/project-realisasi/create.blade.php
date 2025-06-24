<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Realisasi RAB - {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 p-6">
    <div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
        <h1 class="text-xl font-bold mb-4">Tambah Realisasi RAB</h1>

        <div class="mb-4">
            <p><strong>Proyek:</strong> {{ $project->name }}</p>
            <p><strong>Klien:</strong> {{ $project->client->name ?? '-' }}</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('realisation-rab-items.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="project_request_id" value="{{ $project->id }}">

            <div>
                <label for="rencana_anggaran_biaya_id" class="block font-semibold">Item RAB</label>
                <select name="rencana_anggaran_biaya_id" id="rencana_anggaran_biaya_id"
                    class="w-full border rounded p-2" required>
                    @foreach ($rabItems as $item)
                        <option value="{{ $item->id }}">{{ $item->description }} (Rp
                            {{ number_format($item->total, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block font-semibold">Deskripsi</label>
                <input type="text" name="description" id="description" class="w-full border rounded p-2" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="qty" class="block font-semibold">Qty</label>
                    <input type="number" name="qty" id="qty" class="w-full border rounded p-2" required>
                </div>

                <div>
                    <label for="harga" class="block font-semibold">Harga</label>
                    <input type="number" name="harga" id="harga" class="w-full border rounded p-2" required>
                </div>
            </div>

            <div>
                <label for="tanggal_realisasi" class="block font-semibold">Tanggal Realisasi</label>
                <input type="date" name="tanggal_realisasi" id="tanggal_realisasi" class="w-full border rounded p-2"
                    required>
            </div>

            <div>
                <label for="keterangan" class="block font-semibold">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="w-full border rounded p-2"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Realisasi
                </button>
            </div>
        </form>
    </div>
</body>

</html>
