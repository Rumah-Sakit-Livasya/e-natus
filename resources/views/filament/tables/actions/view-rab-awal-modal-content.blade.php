<div>
    {{-- BAGIAN BMHP --}}
    <div class="mb-8" @style('margin-bottom: 5rem')>
        <h3 class="text-lg font-semibold mb-2">Rencana BMHP</h3>
        <x-filament-tables::table>
            <x-slot:header>
                <x-filament-tables::header-cell>Nama BMHP</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Jumlah</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Harga Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Total</x-filament-tables::header-cell>
            </x-slot:header>

            @forelse ($record->projectBmhp as $item)
                <x-filament-tables::row>
                    <x-filament-tables::cell>{{ $item->bmhp->name }}</x-filament-tables::cell>
                    <x-filament-tables::cell>{{ $item->bmhp->satuan }}</x-filament-tables::cell>
                    <x-filament-tables::cell>{{ $item->jumlah_rencana }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->harga_satuan, 0, ',', '.') }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->total, 0, ',', '.') }}</x-filament-tables::cell>
                </x-filament-tables::row>
            @empty
                <x-filament-tables::row>
                    <x-filament-tables::cell colspan="5" class="text-center text-gray-500">
                        Belum ada data BMHP.
                    </x-filament-tables::cell>
                </x-filament-tables::row>
            @endforelse

            <x-slot:footer>
                <x-filament-tables::row>
                    <x-filament-tables::header-cell colspan="4" class="text-right text-base font-semibold">Sub-Total
                        BMHP</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right text-base font-semibold">
                        Rp {{ number_format($record->projectBmhp->sum('total'), 0, ',', '.') }}
                    </x-filament-tables::header-cell>
                </x-filament-tables::row>
            </x-slot:footer>
        </x-filament-tables::table>
    </div>

    {{-- BAGIAN BIAYA OPERASIONAL --}}
    <div class="mb-8" @style('margin-bottom: 5rem')>
        <h3 class="text-lg font-semibold mb-2">Rencana Biaya Operasional</h3>
        <x-filament-tables::table>
            <x-slot:header>
                <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Qty</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Harga Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Total Anggaran</x-filament-tables::header-cell>
            </x-slot:header>

            @forelse ($record->rabOperasionalItems as $item)
                <x-filament-tables::row>
                    <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                    <x-filament-tables::cell>{{ $item->qty_aset }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->harga_sewa, 0, ',', '.') }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->total, 0, ',', '.') }}</x-filament-tables::cell>
                </x-filament-tables::row>
            @empty
                <x-filament-tables::row>
                    <x-filament-tables::cell colspan="4" class="text-center">Belum ada data biaya
                        operasional.</x-filament-tables::cell>
                </x-filament-tables::row>
            @endforelse

            <x-slot:footer>
                <x-filament-tables::row>
                    <x-filament-tables::header-cell colspan="3" class="text-right text-base font-semibold">Sub-Total
                        Operasional</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right text-base font-semibold">
                        Rp {{ number_format($record->rabOperasionalItems->sum('total'), 0, ',', '.') }}
                    </x-filament-tables::header-cell>
                </x-filament-tables::row>
            </x-slot:footer>
        </x-filament-tables::table>
    </div>

    {{-- BAGIAN BIAYA FEE --}}
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Rencana Biaya Fee</h3>
        <x-filament-tables::table>
            <x-slot:header>
                <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Qty</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Harga Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell>Total Anggaran</x-filament-tables::header-cell>
            </x-slot:header>

            @forelse ($record->rabFeeItems as $item)
                <x-filament-tables::row>
                    <x-filament-tables::cell>{{ $item->description }}</x-filament-tables::cell>
                    <x-filament-tables::cell>{{ $item->qty_aset }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->harga_sewa, 0, ',', '.') }}</x-filament-tables::cell>
                    <x-filament-tables::cell>Rp
                        {{ number_format($item->total, 0, ',', '.') }}</x-filament-tables::cell>
                </x-filament-tables::row>
            @empty
                <x-filament-tables::row>
                    <x-filament-tables::cell colspan="4" class="text-center">Belum ada data biaya
                        fee.</x-filament-tables::cell>
                </x-filament-tables::row>
            @endforelse

            <x-slot:footer>
                <x-filament-tables::row>
                    <x-filament-tables::header-cell colspan="3" class="text-right text-base font-semibold">Sub-Total
                        Fee</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right text-base font-semibold">
                        Rp {{ number_format($record->rabFeeItems->sum('total'), 0, ',', '.') }}
                    </x-filament-tables::header-cell>
                </x-filament-tables::row>
            </x-slot:footer>
        </x-filament-tables::table>
    </div>

    {{-- GRAND TOTAL --}}
    <hr class="my-4">
    <div class="flex flex-col items-end pr-4 space-y-2">
        <div class="flex items-center text-base font-semibold text-gray-700">
            <span class="mr-4">Grand Total RAB Awal:</span>
            <span>
                Rp
                {{ number_format(
                    $record->projectBmhp->sum('total') +
                        $record->rabOperasionalItems->sum('total') +
                        $record->rabFeeItems->sum('total'),
                    0,
                    ',',
                    '.',
                ) }}
            </span>
        </div>
        <div class="flex items-center text-lg font-bold">
            <span class="mr-4">Nilai Invoice:</span>
            <span>Rp {{ number_format($record->nilai_invoice ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
