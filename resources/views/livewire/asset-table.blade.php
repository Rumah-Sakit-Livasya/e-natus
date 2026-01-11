<table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead>
        <tr>
            <th class="text-left px-4 py-2 font-semibold text-gray-700">Nama Aset</th>
            <th class="text-left px-4 py-2 font-semibold text-gray-700">Kode</th>
            <th class="text-left px-4 py-2 font-semibold text-gray-700">Aksi</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
        @forelse ($assets as $asset)
            <tr>
                <td class="px-4 py-2">{{ $asset->custom_name }}</td>
                <td class="px-4 py-2">{{ $asset->code }}</td>
                <td class="px-4 py-2">
                    @if ($asset->status !== 'unavailable')
                        <button wire:click="markUnavailable({{ $asset->id }})"
                            class="text-green-600 hover:text-green-800" title="Tandai Unavailable">
                            <x-heroicon-o-check class="w-5 h-5" />
                        </button>
                    @else
                        <span class="text-gray-400 italic">
                            Sedang Dipakai Pada Project:
                            {{ $asset->projectRequests->pluck('name')->first() ?? '-' }}
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-2 text-gray-500 italic">Tidak ada aset terhubung.</td>
            </tr>
        @endforelse
    </tbody>
</table>
