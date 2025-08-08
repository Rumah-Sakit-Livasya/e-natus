<x-filament-panels::page>

    @if ($project)
        {{-- =============================================== --}}
        {{-- BAGIAN RINGKASAN ATAS (CARDS) --}}
        {{-- =============================================== --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- KARTU 1: RENCANA ANGGARAN AWAL --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-document-text class="w-6 h-6 text-gray-500" />
                    <span>Rencana Anggaran Awal</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <div class="text-2xl font-bold tracking-tight">
                        Rp {{ number_format($this->totalAwal(), 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::card>

            {{-- KARTU 2: RAB CLOSING (PENGGANTI REALISASI) --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-gray-500" />
                    <span>RAB Closing (Final)</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <div class="text-2xl font-bold tracking-tight text-primary-600">
                        Rp {{ number_format($this->totalClosing(), 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::card>

            {{-- KARTU 3: SELISIH --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-arrows-right-left class="w-6 h-6 text-gray-500" />
                    <span>Selisih (Closing vs Awal)</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <div @class([
                        'text-2xl font-bold tracking-tight',
                        'text-success-600' => $this->selisihVsAwal() <= 0, // Lebih kecil atau sama dengan = bagus (hemat)
                        'text-danger-600' => $this->selisihVsAwal() > 0, // Lebih besar = jelek (boros)
                    ])>
                        {{ $this->selisihVsAwal() < 0 ? '-' : '+' }} Rp
                        {{ number_format(abs($this->selisihVsAwal()), 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 text-sm text-gray-500">
                        @if ($this->selisihVsAwal() < 0)
                            <x-heroicon-s-arrow-trending-down class="h-4 w-4" />
                            <span>Lebih hemat dari anggaran awal</span>
                        @elseif ($this->selisihVsAwal() > 0)
                            <x-heroicon-s-arrow-trending-up class="h-4 w-4" />
                            <span>Melebihi anggaran awal</span>
                        @else
                            <x-heroicon-s-check-circle class="h-4 w-4" />
                            <span>Sesuai dengan anggaran awal</span>
                        @endif
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- =============================================== --}}
        {{-- BAGIAN DETAIL: TABEL PERBANDINGAN LENGKAP --}}
        {{-- =============================================== --}}
        <x-filament::card heading="Detail Perbandingan Item" class="mt-6">
            <x-filament-tables::table>
                <x-slot:header>
                    <x-filament-tables::header-cell>DESKRIPSI</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right">Rencana Awal
                        (Rp)</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right">Biaya Closing
                        (Rp)</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right">Selisih (Rp)</x-filament-tables::header-cell>
                </x-slot:header>

                @forelse ($this->comparisonData() as $item)
                    @php
                        // Selisih per item: closing - awal
                        $itemSelisih = $item['closing_total'] - $item['awal_total'];
                    @endphp
                    <x-filament-tables::row>
                        <x-filament-tables::cell>{{ $item['description'] }}</x-filament-tables::cell>
                        <x-filament-tables::cell class="text-right">
                            {{ number_format($item['awal_total'], 0, ',', '.') }}
                        </x-filament-tables::cell>
                        <x-filament-tables::cell class="text-right text-primary-600 font-medium">
                            {{ number_format($item['closing_total'], 0, ',', '.') }}
                        </x-filament-tables::cell>
                        <x-filament-tables::cell @class([
                            'text-right font-semibold',
                            'text-success-600' => $itemSelisih <= 0,
                            'text-danger-600' => $itemSelisih > 0,
                        ])>
                            {{ $itemSelisih < 0 ? '-' : ($itemSelisih > 0 ? '+' : '') }}
                            {{ number_format(abs($itemSelisih), 0, ',', '.') }}
                        </x-filament-tables::cell>
                    </x-filament-tables::row>
                @empty
                    <x-filament-tables::row>
                        <x-filament-tables::cell :colspan="4">
                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                <x-heroicon-o-x-circle class="w-12 h-12 text-gray-400 mb-2" />
                                <p class="font-semibold">Tidak Ada Data Item</p>
                            </div>
                        </x-filament-tables::cell>
                    </x-filament-tables::row>
                @endforelse
            </x-filament-tables::table>
        </x-filament::card>
    @else
        {{-- Tampilan jika tidak ada project yang dipilih --}}
        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-8 text-center">
                <x-heroicon-o-folder-open class="w-16 h-16 text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-700">Project Tidak Ditemukan</h3>
                <p class="text-sm text-gray-500">Silakan kembali dan pilih project untuk dibandingkan.</p>
            </div>
        </x-filament::card>
    @endif
</x-filament-panels::page>
