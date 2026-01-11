<div>
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow max-w-lg w-full">
                <h2 class="text-lg font-semibold mb-4">Tambah Realisasi RAB</h2>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block font-semibold">Item RAB</label>
                        <select wire:model="rencana_anggaran_biaya_id" class="w-full border rounded p-2">
                            <option value="">-- Pilih --</option>
                            @foreach ($rabItems as $item)
                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold">Deskripsi</label>
                        <input wire:model="description" type="text" class="w-full border rounded p-2">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold">Qty</label>
                            <input wire:model="qty" type="number" class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block font-semibold">Harga</label>
                            <input wire:model="harga" type="number" class="w-full border rounded p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold">Tanggal Realisasi</label>
                        <input wire:model="tanggal_realisasi" type="date" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block font-semibold">Keterangan</label>
                        <textarea wire:model="keterangan" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
