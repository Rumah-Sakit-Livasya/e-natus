@php
    $formatAssetCode = function (?string $code): string {
        if (! filled($code)) {
            return '-';
        }

        $parts = explode('/', (string) $code);
        if (count($parts) >= 4) {
            $code = "{$parts[0]}/{$parts[count($parts) - 2]}/{$parts[count($parts) - 1]}";
        }

        $normalizedParts = array_map(
            fn($part) => \Illuminate\Support\Str::upper((string) preg_replace('/\s+/', '-', trim((string) $part))),
            explode('/', (string) $code),
        );

        return implode('/', $normalizedParts);
    };
@endphp

<div class="p-2">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="px-3 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Aset</th>
                <th class="px-3 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kode</th>
                <th class="px-3 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Kontrol
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($assets as $asset)
                <tr wire:key="asset-{{ $asset->id }}" class="group transition-colors duration-200">
                    <td class="px-3 py-4">
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Str::upper((string) $asset->custom_name) }}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">ID: #{{ $asset->id }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4">
                        <span
                            class="px-2 py-0.5 text-[11px] font-mono bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-md border border-gray-100 dark:border-gray-700">
                            {{ $formatAssetCode($asset->code) }}
                        </span>
                    </td>
                    <td class="px-3 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @if ($asset->status !== 'unavailable')
                                <button wire:click="markUnavailable({{ $asset->id }})"
                                    class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-success-500/30 bg-success-500/10 px-3 text-[11px] font-semibold text-success-600 transition hover:bg-success-500/20 disabled:cursor-not-allowed disabled:opacity-60 dark:text-success-400"
                                    type="button" wire:loading.attr="disabled"
                                    wire:target="markUnavailable({{ $asset->id }})">
                                    <x-heroicon-m-check-circle class="w-4 h-4" />
                                    Pakai Aset
                                </button>
                            @else
                                <div class="flex items-center gap-2">
                                    <button wire:click="markAvailable({{ $asset->id }})"
                                        class="inline-flex h-9 min-w-[110px] items-center justify-center gap-1.5 rounded-lg border border-primary-500/30 bg-primary-500/10 px-3 text-[11px] font-semibold text-primary-600 transition hover:bg-primary-500/20 disabled:cursor-not-allowed disabled:opacity-60 dark:text-primary-400"
                                        type="button" wire:loading.attr="disabled"
                                        wire:target="markAvailable({{ $asset->id }})">
                                        <x-heroicon-m-arrow-uturn-left class="h-4 w-4" wire:loading.remove
                                            wire:target="markAvailable({{ $asset->id }})" />
                                        <x-heroicon-m-arrow-path class="h-4 w-4 animate-spin" wire:loading
                                            wire:target="markAvailable({{ $asset->id }})" />
                                        <span wire:loading.remove wire:target="markAvailable({{ $asset->id }})">Kembalikan</span>
                                        <span wire:loading wire:target="markAvailable({{ $asset->id }})">Memproses</span>
                                    </button>

                                    <div class="relative">
                                        <button wire:click="initiateSwap({{ $asset->id }})"
                                            class="inline-flex h-9 min-w-[86px] items-center justify-center gap-1.5 rounded-lg border border-warning-500/30 bg-warning-500/10 px-3 text-[11px] font-semibold text-warning-600 transition hover:bg-warning-500/20 dark:text-warning-400"
                                            type="button">
                                            <x-heroicon-m-arrows-right-left class="h-4 w-4" />
                                            <span>Tukar</span>
                                        </button>

                                        @if ($swapTargetId === $asset->id)
                                            <div
                                                class="fixed md:absolute z-50 right-0 mt-2 w-72 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                                                <div
                                                    class="p-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                                                    <div class="flex flex-col items-start">
                                                        <span
                                                            class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tighter">Tukar
                                                            Aset</span>
                                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">Pilih aset pengganti yang tersedia</span>
                                                    </div>
                                                    <button wire:click="cancelSwap"
                                                        class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full transition-colors">
                                                        <x-heroicon-s-x-mark class="w-3.5 h-3.5 text-gray-400" />
                                                    </button>
                                                </div>

                                                <div class="p-2">
                                                    <div class="relative group">
                                                        <x-heroicon-m-magnifying-glass
                                                            class="absolute left-3 top-2.5 w-3.5 h-3.5 text-gray-400 transition-colors group-focus-within:text-primary-500" />
                                                        <input type="text" wire:model.live.debounce.300ms="search"
                                                            placeholder="Cari nama atau kode..."
                                                            class="w-full pl-9 pr-3 py-2 text-xs bg-gray-100 dark:bg-gray-800 border-none rounded-lg focus:ring-1 focus:ring-primary-500 text-gray-900 dark:text-gray-100 outline-none transition-all"
                                                            autofocus>
                                                    </div>
                                                </div>

                                                <div class="max-h-56 overflow-y-auto p-1 space-y-1 custom-scrollbar">
                                                    @forelse($availableAssets as $available)
                                                        <button
                                                            wire:click="swapAsset({{ $asset->id }}, {{ $available->id }})"
                                                            class="w-full text-left p-3 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all flex flex-col gap-0.5 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 group">
                                                            <span
                                                                class="text-xs font-bold text-gray-800 dark:text-gray-200 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors">
                                                                {{ \Illuminate\Support\Str::upper((string) $available->custom_name) }}
                                                            </span>
                                                            <span
                                                                class="text-[9px] font-mono text-gray-500 dark:text-gray-400">
                                                                {{ $formatAssetCode($available->code) }}
                                                            </span>
                                                        </button>
                                                    @empty
                                                        <div class="py-10 text-center flex flex-col items-center gap-2">
                                                            <x-heroicon-o-archive-box-x-mark
                                                                class="w-8 h-8 text-gray-200 dark:text-gray-800" />
                                                            <p class="text-[10px] text-gray-400 font-medium">Aset tidak
                                                                ditemukan</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-full">
                                <x-heroicon-o-face-frown class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                            </div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Belum ada aset
                                terhubung</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }
    </style>
</div>
