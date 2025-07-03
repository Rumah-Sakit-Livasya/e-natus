<div>
    {{-- Bagian Header dengan Tombol Aksi Filament --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
        <div>
            {{ $this->printRabAction }}
            {{ $this->printRealisasiAction }}
        </div>
        <div>
            {{ $this->createRealisasiAction }}
        </div>
    </div>

    {{-- Gunakan Komponen Tabs Bawaan Filament --}}
    <x-filament::tabs>
        {{-- Tab 1: Rencana Anggaran Biaya --}}
        <x-filament::tabs.item :label="'Rencana Anggaran Biaya'" icon="heroicon-m-clipboard-document-list">

            <x-filament::card class="mt-4">
                {{-- PERBAIKAN: Gunakan namespace filament-tables:: --}}
                <x-filament-tables::table>
                    <x-slot:header>
                        <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Qty</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Harga Satuan</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Total
                            Anggaran</x-filament-tables::header-cell>
                    </x-slot:header>

                    @forelse ($rabItems as $item)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">{{ $item->qty_aset }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->harga_sewa, 0, ',', '.') }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->total, 0, ',', '.') }}</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @empty
                        <x-filament-tables::row>
                            <x-filament-tables::cell colspan="4" class="text-center">
                                Belum ada data anggaran.
                            </x-filament-tables::cell>
                        </x-filament-tables::row>
                    @endforelse

                    <x-slot:footer>
                        <x-filament-tables::row>
                            <x-filament-tables::header-cell colspan="3" class="text-right text-base">
                                Total Anggaran
                            </x-filament-tables::header-cell>
                            <x-filament-tables::header-cell class="text-right text-base">
                                Rp {{ number_format($totalAnggaran, 0, ',', '.') }}
                            </x-filament-tables::header-cell>
                        </x-filament-tables::row>
                    </x-slot:footer>
                </x-filament-tables::table>
            </x-filament::card>

        </x-filament::tabs.item>

        {{-- Tab 2: Realisasi Biaya --}}
        <x-filament::tabs.item :label="'Realisasi Biaya'" icon="heroicon-m-check-circle">

            <x-filament::card class="mt-4">
                {{-- PERBAIKAN: Gunakan namespace filament-tables:: --}}
                <x-filament-tables::table>
                    <x-slot:header>
                        <x-filament-tables::header-cell>Item Anggaran</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell>Deskripsi Realisasi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Qty</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Harga
                            Realisasi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Total
                            Realisasi</x-filament-tables::header-cell>
                    </x-slot:header>

                    @forelse ($realisasiItems as $item)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>{{ $item->rabItem?->description ?? 'N/A' }}</x-filament-tables::cell>
                            <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">{{ $item->qty }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->harga, 0, ',', '.') }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->total, 0, ',', '.') }}</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @empty
                        <x-filament-tables::row>
                            <x-filament-tables::cell colspan="5" class="text-center">
                                Belum ada data realisasi.
                            </x-filament-tables::cell>
                        </x-filament-tables::row>
                    @endforelse

                    <x-slot:footer>
                        <x-filament-tables::row>
                            <x-filament-tables::header-cell colspan="4" class="text-right text-base">
                                Total Realisasi
                            </x-filament-tables::header-cell>
                            <x-filament-tables::header-cell class="text-right text-base">
                                Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                            </x-filament-tables::header-cell>
                        </x-filament-tables::row>
                    </x-slot:footer>
                </x-filament-tables::table>
            </x-filament::card>

        </x-filament::tabs.item>
    </x-filament::tabs>

    {{-- Bagian Summary/Ringkasan Akhir di bawah tabs --}}
    <x-filament::section class="mt-6">
        <div class="px-4 py-4 sm:px-6">
            {{--
                PERBAIKAN:
                - Menggunakan `justify-between` untuk mendistribusikan item.
                - Menghapus `gap-x-*` karena jarak diatur oleh `justify-between`.
                - Menghapus `text-right` dari parent agar bisa diatur per item.
            --}}
            <dl class="flex items-center justify-between">
                {{-- Item 1: Dibuat rata kiri --}}
                <div class="flex flex-col text-left">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Anggaran</dt>
                    <dd class="text-lg font-semibold">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</dd>
                </div>

                {{-- Item 2: Dibuat rata tengah --}}
                <div class="flex flex-col text-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Realisasi</dt>
                    <dd class="text-lg font-semibold text-primary-600">Rp
                        {{ number_format($totalRealisasi, 0, ',', '.') }}</dd>
                </div>

                {{-- Item 3: Dibuat rata kanan --}}
                <div class="flex flex-col text-right">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Selisih</dt>
                    <dd class="text-lg font-semibold {{ $selisih >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                        Rp {{ number_format($selisih, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </div>
    </x-filament::section>
    {{-- Komponen wajib untuk modal Filament --}}
    <x-filament-actions::modals />
</div>
