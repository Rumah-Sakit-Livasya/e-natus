<div>
    {{-- Di sini Anda bisa menempatkan tombol aksi jika diperlukan di masa depan --}}
    {{-- Contoh:
    <div class="flex justify-end mb-4">
        {{ $this->printAction }}
    </div>
    --}}

    {{-- Gunakan Komponen Tabs Bawaan Filament --}}
    <x-filament::tabs>
        {{-- ======================================================= --}}
        {{-- Tab 1: Rencana Anggaran Biaya (RAB Awal) - TIDAK BERUBAH --}}
        {{-- ======================================================= --}}
        <x-filament::tabs.item :label="'Rencana Anggaran Biaya'" icon="heroicon-m-clipboard-document-list">
            <x-filament::card class="mt-4">
                <x-filament-tables::table>
                    <x-slot:header>
                        <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Qty</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Harga Satuan</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Total
                            Anggaran</x-filament-tables::header-cell>
                    </x-slot:header>

                    @forelse ($rabAwalItems as $item)
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
                            <x-filament-tables::cell colspan="4" class="text-center">Belum ada data
                                anggaran.</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @endforelse

                    <x-slot:footer>
                        <x-filament-tables::row>
                            <x-filament-tables::header-cell colspan="3"
                                class="text-right text-base font-semibold">Total Anggaran
                                Awal</x-filament-tables::header-cell>
                            <x-filament-tables::header-cell class="text-right text-base font-semibold">Rp
                                {{ number_format($totalRabAwal, 0, ',', '.') }}</x-filament-tables::header-cell>
                        </x-filament-tables::row>
                    </x-slot:footer>
                </x-filament-tables::table>
            </x-filament::card>
        </x-filament::tabs.item>

        {{-- =================================================== --}}
        {{-- Tab 2: RAB Closing (PERUBAHAN UTAMA DI SINI) --}}
        {{-- =================================================== --}}
        <x-filament::tabs.item :label="'RAB Closing'" icon="heroicon-m-check-circle">
            <x-filament::card class="mt-4">
                {{-- Tabel untuk Item Operasional dari RAB Closing --}}
                <h3 class="text-lg font-semibold mb-2">Item Operasional (Closing)</h3>
                <x-filament-tables::table class="mb-6">
                    <x-slot:header>
                        <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Total Biaya</x-filament-tables::header-cell>
                    </x-slot:header>

                    @forelse ($rabClosingOperasionalItems as $item)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->price, 0, ',', '.') }}</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @empty
                        <x-filament-tables::row>
                            <x-filament-tables::cell colspan="2" class="text-center">Belum ada data
                                operasional.</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @endforelse
                </x-filament-tables::table>

                {{-- Tabel untuk Item Fee Petugas dari RAB Closing --}}
                <h3 class="text-lg font-semibold mb-2 mt-4">Item Fee Petugas (Closing)</h3>
                <x-filament-tables::table>
                    <x-slot:header>
                        <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                        <x-filament-tables::header-cell class="text-right">Total Biaya</x-filament-tables::header-cell>
                    </x-slot:header>

                    @forelse ($rabClosingFeeItems as $item)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                            <x-filament-tables::cell class="text-right">Rp
                                {{ number_format($item->price, 0, ',', '.') }}</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @empty
                        <x-filament-tables::row>
                            <x-filament-tables::cell colspan="2" class="text-center">Belum ada data fee
                                petugas.</x-filament-tables::cell>
                        </x-filament-tables::row>
                    @endforelse
                </x-filament-tables::table>
            </x-filament::card>

            {{-- Footer untuk Total RAB Closing --}}
            <div class="mt-4 rounded-lg border bg-gray-50 dark:border-white/10 dark:bg-white/5">
                <div class="px-6 py-3">
                    <div class="flex items-center justify-end gap-x-3">
                        <span class="text-base font-semibold">Total RAB Closing</span>
                        <span class="text-base font-semibold">Rp
                            {{ number_format($totalRabClosing, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </x-filament::tabs.item>
    </x-filament::tabs>

    {{-- =================================================== --}}
    {{-- Bagian Summary/Ringkasan (PERUBAHAN DI SINI) --}}
    {{-- =================================================== --}}
    <x-filament::section class="mt-6">
        <div class="px-4 py-4 sm:px-6">
            <dl class="flex items-center justify-between">
                {{-- Item 1: Total Anggaran Awal --}}
                <div class="flex flex-col text-left">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Anggaran Awal</dt>
                    <dd class="text-lg font-semibold">Rp {{ number_format($totalRabAwal, 0, ',', '.') }}</dd>
                </div>

                {{-- Item 2: Total RAB Closing --}}
                <div class="flex flex-col text-center">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total RAB Closing</dt>
                    <dd class="text-lg font-semibold text-primary-600">Rp
                        {{ number_format($totalRabClosing, 0, ',', '.') }}</dd>
                </div>

                {{-- Item 3: Selisih --}}
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
