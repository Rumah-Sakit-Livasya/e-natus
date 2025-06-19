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
                        <x-filament::link href="{{ route('aset.mark-unavailable', $asset->id) }}" tooltip="Setujui Aset"
                            icon="heroicon-s-check-circle" icon-size="lg" color="success">
                        </x-filament::link>
                    @else
                        <span class="text-gray-400 italic">
                            Sedang Dipakai Pada Project:
                            @php
                                $names = $asset->projectRequests()->pluck('name')->first();
                            @endphp
                            {{ $names }}
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
