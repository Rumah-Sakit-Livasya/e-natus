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
            <tr wire:key="asset-{{ $asset->id }}">
                <td class="px-4 py-2">{{ $asset->custom_name }}</td>
                <td class="px-4 py-2">{{ $asset->code }}</td>
                <td class="px-4 py-2">
                    @if ($asset->status !== 'unavailable')
                        <button wire:click="markUnavailable({{ $asset->id }})"
                            class="text-green-600 hover:text-green-800" type="button" wire:loading.attr="disabled"
                            wire:target="markUnavailable({{ $asset->id }})">
                            <x-heroicon-s-check-circle class="w-6 h-6" />
                        </button>
                    @else
                        <div class="flex items-center space-x-2">
                            <button wire:click="markAvailable({{ $asset->id }})"
                                class="text-blue-600 hover:text-blue-800 flex items-center space-x-1" type="button"
                                wire:loading.attr="disabled" wire:target="markAvailable({{ $asset->id }})">
                                <x-heroicon-s-backspace class="w-5 h-5" />
                                <span class="text-xs font-semibold">Kembalikan Aset</span>
                            </button>
                        </div>
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
