<div>
    @if ($showModal)
        <x-filament::modal id="asset-details-modal" max-width="7xl" closeable wire:model="showModal">
            <x-slot name="title">
                {{ $modalTitle }}
            </x-slot>

            <div>
                {{ $this->table }}
            </div>
        </x-filament::modal>
    @endif
</div>
