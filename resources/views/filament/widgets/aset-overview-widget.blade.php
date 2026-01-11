<x-filament::widget>
    <x-filament::stats-overview>
        @foreach ($this->getStats() as $stat)
            {{ $stat }}
        @endforeach
    </x-filament::stats-overview>

    {{-- Modal detail aset --}}
    @if ($modalType)
        <x-filament::modal id="modal-detail-aset" :visible="true" :slide-over="false" :close-by-clicking-away="true" :close-by-pressing-escape="true"
            width="3xl" wire:close="closeModal">
            <x-slot name="heading">
                {{ $modalType === 'available' ? 'Detail Aset Tersedia' : 'Detail Aset Terpakai' }}
            </x-slot>

            <x-slot name="content">
                <ul class="space-y-2">
                    @foreach (\App\Models\Aset::where('status', $modalType)->get() as $aset)
                        <li class="border-b pb-2">
                            <strong>{{ $aset->custom_name }}</strong>
                            @if ($modalType === 'unavailable')
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Dipakai di: {{ $aset->projectRequests()->pluck('name')->join(', ') }}
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-slot>

            <x-slot name="footer">
                <x-filament::button wire:click="closeModal">Tutup</x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif
</x-filament::widget>
