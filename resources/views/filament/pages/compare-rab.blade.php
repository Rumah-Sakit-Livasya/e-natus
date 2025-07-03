<x-filament-panels::page>
    {{--
        Bagian statistik sekarang ditangani oleh getHeaderWidgets() di file PHP Page,
        jadi tidak perlu ada kode statistik di sini. Filament akan menampilkannya secara otomatis.
    --}}

    {{-- Tabel Perbandingan Detail --}}
    <div class="mt-8">
        <x-filament::section>
            <x-slot name="heading">
                Detail Perbandingan Anggaran vs Realisasi
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    {{-- INI BAGIAN HEADER TABEL --}}
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Deskripsi Pekerjaan</th>
                            <th scope="col" class="px-6 py-3 text-right">Anggaran</th>
                            <th scope="col" class="px-6 py-3 text-right">Realisasi</th>
                            <th scope="col" class="px-6 py-3 text-right">Selisih</th>
                        </tr>
                    </thead>

                    {{-- INI BAGIAN ISI TABEL --}}
                    <tbody>
                        {{-- Lakukan looping pada setiap item dari RAB Closing --}}
                        @forelse ($this->rabClosing->items as $item)
                            @php
                                // Ambil data realisasi yang sudah dikelompokkan dari file PHP Page
                                // Variabel $this->realisationsGrouped sudah disiapkan di metode mount()
                                $realisasi = $this->realisationsGrouped[$item->description] ?? null;
                                $totalRealisasiItem = $realisasi ? $realisasi->total_realisasi : 0;
                                $selisihItem = $item->total_anggaran - $totalRealisasiItem;
                            @endphp
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{-- Kolom Deskripsi --}}
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->description }}
                                </th>
                                {{-- Kolom Anggaran --}}
                                <td class="px-6 py-4 text-right">
                                    {{ 'Rp ' . number_format($item->total_anggaran, 0, ',', '.') }}
                                </td>
                                {{-- Kolom Realisasi --}}
                                <td class="px-6 py-4 text-right">
                                    {{ 'Rp ' . number_format($totalRealisasiItem, 0, ',', '.') }}
                                </td>
                                {{-- Kolom Selisih dengan pewarnaan dinamis --}}
                                <td @class([
                                    'px-6 py-4 text-right font-semibold',
                                    'text-green-600 dark:text-green-400' => $selisihItem >= 0,
                                    'text-red-600 dark:text-red-400' => $selisihItem < 0,
                                ])>
                                    {{ ($selisihItem < 0 ? '-' : '') . 'Rp ' . number_format(abs($selisihItem), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            {{-- Tampilan jika tidak ada item sama sekali di RAB Closing --}}
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada item anggaran pada RAB Closing ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
