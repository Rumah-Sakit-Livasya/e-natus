<div>
    {{-- BAGIAN BIAYA OPERASIONAL --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Rencana Biaya Operasional</h3>
        <x-filament-tables::table>
            <x-slot:header>
                <x-filament-tables::header-cell>Deskripsi</x-filament-tables::header-cell>
                <x-filament-tables::header-cell class="text-right">Qty</x-filament-tables::header-cell>
                <x-filament-tables::header-cell class="text-right">Harga Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell class="text-right">Total Anggaran</x-filament-tables::header-cell>
            </x-slot:header>

            @forelse ($record->rabOperasionalItems as $item)
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
                <x-filament-tables::header-cell class="text-right">Qty</x-filament-tables::header-cell>
                <x-filament-tables::header-cell class="text-right">Harga Satuan</x-filament-tables::header-cell>
                <x-filament-tables::header-cell class="text-right">Total Anggaran</x-filament-tables::header-cell>
            </x-slot:header>

            @forelse ($record->rabFeeItems as $item)
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
                {{ number_format($record->rabOperasionalItems->sum('total') + $record->rabFeeItems->sum('total'), 0, ',', '.') }}
            </span>
        </div>
        <div class="flex items-center  text-lg font-bold">
            <span class="mr-4">Nilai Invoice:</span>
            <span>
                Rp
                {{ number_format($record->nilai_invoice ?? 0, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>
